<?php
/**
 * The main WP Offload SES plugin class.
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\Queue\Email_Queue;
use DeliciousBrains\WP_Offload_SES\Queue\Queue_Status;
use Exception;
use phpmailerException;

/**
 * Class WP_Offload_SES
 *
 * @since 1.0.0
 */
class WP_Offload_SES extends Plugin_Base {

	const SETTINGS_KEY      = 'wposes_settings';
	const SETTINGS_CONSTANT = 'WPOSES_SETTINGS';

	/**
	 * The plugin hook suffix.
	 *
	 * @var string
	 */
	public $hook_suffix;

	/**
	 * The plugin title.
	 *
	 * @var string
	 */
	protected $plugin_title;

	/**
	 * The plugin menu title.
	 *
	 * @var string
	 */
	protected $plugin_menu_title;

	/**
	 * The plugin prefix.
	 *
	 * @var string
	 */
	protected $plugin_prefix = 'wposes';

	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	protected $plugin_slug = 'wp-ses';

	/**
	 * The Amazon_Web_Services class.
	 *
	 * @var Amazon_Web_Services
	 */
	private $aws;

	/**
	 * The SES_API class.
	 *
	 * @var SES_API
	 */
	private $ses_api;

	/**
	 * The Email_Log class.
	 *
	 * @var Email_Log
	 */
	private $email_log;

	/**
	 * The Email_Events class.
	 *
	 * @var Email_Events
	 */
	private $email_events;

	/**
	 * The Email_Queue class.
	 *
	 * @var Email_Queue
	 */
	private $email_queue;

	/**
	 * The Notices class.
	 *
	 * @var Notices
	 */
	private $notices;

	/**
	 * The Health_Report class.
	 *
	 * @var Health_Report
	 */
	protected $health_report;

	/**
	 * The Queue_Status class.
	 *
	 * @var Queue_Status
	 */
	private $queue_status;

	/**
	 * The Attachments class.
	 *
	 * @var Attachments
	 */
	private $attachments;

	/**
	 * Construct the plugin base and initialize the plugin.
	 *
	 * @param string $plugin_file_path The plugin file path.
	 *
	 * @throws Exception
	 */
	public function __construct( $plugin_file_path ) {
		parent::__construct( $plugin_file_path );
		$this->init( $plugin_file_path );
	}

	/**
	 * Initialize the plugin.
	 *
	 * @param string $plugin_file_path The plugin file path.
	 *
	 * @throws Exception
	 */
	public function init( string $plugin_file_path ) {
		// Set the name of the plugin.
		$this->plugin_title      = $this->get_plugin_page_title();
		$this->plugin_menu_title = $this->get_plugin_page_title();

		// Initialize any necessary classes.
		$this->aws          = new Amazon_Web_Services( $this );
		$this->ses_api      = new SES_API();
		$this->email_log    = new Email_Log();
		$this->email_events = new Email_Events();
		$this->email_queue  = new Email_Queue();
		$this->queue_status = new Queue_Status( $this );
		$this->notices      = Notices::get_instance( $this );
		$this->attachments  = new Attachments();

		if ( ! $this->is_pro() ) {
			$this->health_report = new Health_Report( $this );
		}

		new WP_Notifications( $this );

		// Plugin setup.
		add_action( 'admin_init', array( $this, 'upgrade_routines' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'plugin_action_links', array( $this, 'plugin_actions_settings_link' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'plugin_actions_settings_link' ), 10, 2 );
		add_action( 'pre_current_active_plugins', array( $this, 'plugin_deactivated_notice' ) );
		add_action( 'wposes_plugin_load', array( $this->settings, 'set_default_settings' ) );

		// Event handlers.
		add_action( 'wposes_mail_sent', array( $this, 'maybe_delete_successful' ), 99, 6 );

		// UI AJAX.
		add_action( 'wp_ajax_wposes_activity_table', array( $this, 'ajax_activity_table' ) );
		add_action( 'wp_ajax_wposes-get-diagnostic-info', array( $this, 'ajax_get_diagnostic_info' ) );
		add_action( 'wp_ajax_wposes-aws-keys-set', array( $this, 'ajax_set_aws_keys' ) );
		add_action( 'wp_ajax_wposes-aws-keys-remove', array( $this, 'ajax_remove_aws_keys' ) );
		add_action( 'wp_ajax_wposes_get_verified_senders_list', array( $this, 'ajax_verified_senders_list' ) );
		add_action( 'wp_ajax_wposes-verify-sender', array( $this, 'ajax_verify_sender' ) );
		add_action( 'wp_ajax_wposes_delete_sender', array( $this, 'ajax_delete_sender' ) );
		add_action( 'wp_ajax_wposes-ajax-save-settings', array( $this, 'ajax_save_settings' ) );
		add_action( 'wp_ajax_wposes-send-test-email', array( $this, 'ajax_send_test_email' ) );
		add_action( 'wp_ajax_wposes-purge-logs', array( $this, 'ajax_purge_logs' ) );
	}

	/**
	 * Perform plugin upgrade routines.
	 *
	 * @param bool $skip_version_check If we should skip the version check.
	 *
	 * @return bool
	 */
	public function upgrade_routines( bool $skip_version_check = false ): bool {
		$version = get_site_option( 'wposes_lite_version', '0.0.0' );

		// Nothing to upgrade.
		if ( ! $skip_version_check && ! version_compare( $version, $this->get_plugin_version(), '<' ) ) {
			return false;
		}

		// Already doing an upgrade.
		if ( get_site_option( 'wposes_doing_upgrade' ) ) {
			return false;
		}

		// Start the upgrade.
		update_site_option( 'wposes_doing_upgrade', true );

		$this->get_email_log()->install_tables();
		$this->get_email_events()->install_tables();
		$this->get_email_queue()->install_tables();
		$this->get_attachments()->install_tables();

		if ( ! get_site_option( 'wposes_tracking_key' ) ) {
			add_site_option( 'wposes_tracking_key', wp_generate_password( 20, true, true ) );
		}

		$this->maybe_migrate_from_wpses();

		// If $skip_version_check is true, this upgrade was initiated by the pro version.
		if ( ! $skip_version_check ) {
			update_site_option( 'wposes_lite_version', $this->get_plugin_version() );
		}

		// All done!
		delete_site_option( 'wposes_doing_upgrade' );

		return true;
	}

	/**
	 * Migrate the settings from WP SES if necessary.
	 *
	 * @return bool
	 */
	private function maybe_migrate_from_wpses(): bool {
		if ( ! is_multisite() ) {
			// Migrate over single site settings.
			return $this->convert_wpses_options();
		}

		// Set free version to use subsite settings by default.
		if ( ! $this->is_pro() ) {
			$settings                            = get_site_option( static::SETTINGS_KEY );
			$settings['enable-subsite-settings'] = true;
			update_site_option( static::SETTINGS_KEY, $settings );
		}

		// Migrate settings for each subsite if set.
		$sites = get_sites( array( 'fields' => 'ids' ) );

		foreach ( $sites as $site => $id ) {
			switch_to_blog( $id );
			$this->convert_wpses_options();
			restore_current_blog();
		}

		return true;
	}

	/**
	 * Convert the old WP SES settings into the new settings.
	 *
	 * @return bool
	 */
	private function convert_wpses_options(): bool {
		$wposes_settings = get_option( 'wposes_settings', array() );

		if ( ! empty( $wposes_settings ) ) {
			// Network settings already overridden.
			if ( is_multisite() && ! isset( $wposes_settings['override-network-settings'] ) ) {
				$wposes_settings['override-network-settings'] = true;
				update_option( 'wposes_settings', $wposes_settings );
			}

			return true;
		}

		$wpses_options = get_option( 'wpses_options', array() );

		// If WP SES hasn't been set up, nothing to do here.
		if ( empty( $wpses_options ) ) {
			return true;
		}

		foreach ( $wpses_options as $key => $value ) {
			switch ( $key ) {
				case 'from_name':
					$wposes_settings['default-email-name'] = $value;
					break;
				case 'from_email':
					$wposes_settings['default-email'] = $value;
					break;
				case 'reply_to':
					if ( 'headers' === $value ) {
						$value = '';
					}
					$wposes_settings['reply-to'] = $value;
					break;
				case 'return_path':
					$wposes_settings['return-path'] = $value;
					break;
				case 'access_key':
					$wposes_settings['aws-access-key-id'] = $value;
					break;
				case 'secret_key':
					$wposes_settings['aws-secret-access-key'] = $value;
					break;
				case 'active':
					$wposes_settings['send-via-ses'] = $value;
					break;
				case 'endpoint':
					if ( '' === $value ) {
						break;
					}
					$value                     = explode( '.', $value );
					$region                    = $value[1];
					$wposes_settings['region'] = $region;
					break;
			}
		}

		$wposes_settings['completed-setup'] = true;
		update_option( 'wposes_settings', $wposes_settings );

		return true;
	}

	/**
	 * Display a notice after either lite or pro plugin has been auto deactivated
	 */
	public function plugin_deactivated_notice() {
		if ( false !== ( $deactivated_notice_id = get_transient( 'wposes_deactivated_notice_id' ) ) ) {
			if ( '1' === $deactivated_notice_id ) {
				$title   = __( 'WP Offload SES Activation', 'wp-offload-ses' );
				$message = __(
					"WP Offload SES Lite and WP Offload SES cannot both be active. We've automatically deactivated WP Offload SES Lite.",
					'wp-offload-ses'
				);
			} else {
				$title   = __( 'WP Offload SES Lite Activation', 'wp-offload-ses' );
				$message = __(
					"WP Offload SES Lite and WP Offload SES cannot both be active. We've automatically deactivated WP Offload SES.",
					'wp-offload-ses'
				);
			}

			$message = sprintf( '<strong>%s</strong> &mdash; %s', esc_html( $title ), esc_html( $message ) );

			$this->render_view( 'notice', array( 'message' => $message ) );

			delete_transient( 'wposes_deactivated_notice_id' );
		}
	}

	/**
	 * Add the WP Offload SES admin page.
	 */
	public function admin_menu() {
		global $submenu;

		if ( is_multisite() && ! is_network_admin() && ! $this->settings->get_setting( 'enable-subsite-settings' ) ) {
			return;
		}

		$this->hook_suffix = add_submenu_page(
			$this->get_plugin_pagenow(),
			$this->get_plugin_page_title(),
			$this->plugin_menu_title,
			'manage_options',
			self::$plugin_page,
			array( $this, 'render_page' )
		);

		// A bit of a hack, but better than doing things the proper way.
		$submenu['index.php'][] = array(
			$this->get_plugin_page_title(),
			'manage_options',
			$this->get_plugin_page_url( array(), 'self' ) . '#reports',
		); // phpcs:ignore

		add_action( 'load-' . $this->hook_suffix, array( $this, 'plugin_load' ) );
	}

	/**
	 * Enqueue any styles/scripts.
	 */
	public function plugin_load() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array(), '1.11.2' );
		wp_enqueue_style( 'jquery-ui' );

		add_action( 'network_admin_notices', array( $this, 'settings_saved_notice' ) );

		$this->enqueue_style( 'wposes-styles', 'assets/css/styles' );
		$this->enqueue_style( 'wposes-modal', 'assets/css/modal' );
		$this->enqueue_script( 'wposes-modal', 'assets/js/modal', array( 'jquery' ) );
		$this->enqueue_script( 'wposes-script', 'assets/js/script', array( 'jquery', 'underscore', 'wposes-modal' ) );
		$this->enqueue_script(
			'wposes-verified-senders',
			'assets/js/verified-senders',
			array( 'wposes-script', 'wposes-modal' )
		);
		$this->enqueue_script( 'wposes-setup', 'assets/js/setup', array( 'jquery' ) );
		$this->enqueue_script( 'wposes-activity', 'assets/js/activity', array( 'wposes-script' ) );

		if ( ! $this->is_pro() ) {
			$this->enqueue_script(
				'wposes-tracking-prompt',
				'assets/js/tracking-prompt',
				array( 'wposes-script', 'wposes-modal' )
			);
			$this->enqueue_script(
				'wposes-health-report-prompt',
				'assets/js/health-report-prompt',
				array( 'wposes-script', 'wposes-modal' )
			);
		}

		wp_localize_script(
			'wposes-script',
			'wposes',
			array(
				'strings'            => apply_filters( 'wposes_js_strings', array(
					'get_diagnostic_info'       => __( 'Getting diagnostic info...', 'wp-offload-ses' ),
					'get_diagnostic_info_error' => __( 'Error getting diagnostic info: ', 'wp-offload-ses' ),
					// Mimic WP Core's notice text, therefore no translation needed here.
					'settings_saved'            => __( 'Settings saved.' ),
					'domain_invalid'            => __(
						'Please enter a valid domain name (without http:// or https://',
						'wp-offload-ses'
					),
					'email_invalid'             => __( 'Please enter a valid email address.', 'wp-offload-ses' ),
					'not_shown_placeholder'     => _x(
						'-- not shown --',
						'placeholder for hidden access key, 39 char max',
						'wp-offload-ses'
					),
					'email_not_verified'        => __(
						'Please enter a valid email address that has been verified with Amazon SES.',
						'wp-offload-ses'
					),
				) ),
				'nonces'             => apply_filters( 'wposes_js_nonces', array(
					'get_diagnostic_info'    => wp_create_nonce( 'wposes-get-diagnostic-info' ),
					'aws_keys_set'           => wp_create_nonce( 'wposes-aws-keys-set' ),
					'aws_keys_remove'        => wp_create_nonce( 'wposes-aws-keys-remove' ),
					'ajax_save_settings'     => wp_create_nonce( 'wposes-ajax-save-settings' ),
					'wposes_verify_sender'   => wp_create_nonce( 'wposes-verify-sender' ),
					'wposes_send_test_email' => wp_create_nonce( 'wposes-send-test-email' ),
					'wposes_purge_logs'      => wp_create_nonce( 'wposes-purge-logs' ),
				) ),
				'is_pro'             => $this->is_pro(),
				'is_setup'           => $this->is_plugin_setup(),
				'show_settings_tabs' => $this->show_settings_tabs(),
				'plugin_url'         => $this->get_plugin_page_url( array(), 'self' ),
				'verified_senders'   => $this->get_verified_senders( true ),
			)
		);

		$this->check_defined_access_keys();
		$this->check_unverified_senders();
		$this->maybe_override_network_settings();
		$this->handle_post_request();
		$this->http_prepare_download_log();
		$this->init_admin_footer();

		do_action( 'wposes_plugin_load' );
	}

	/**
	 * Getter for Amazon_Web_Services.
	 *
	 * @return Amazon_Web_Services
	 */
	public function get_aws(): Amazon_Web_Services {
		return $this->aws;
	}

	/**
	 * Getter for SES_API.
	 *
	 * @return SES_API
	 */
	public function get_ses_api(): SES_API {
		return $this->ses_api;
	}

	/**
	 * Getter for Email_Log.
	 *
	 * @return Email_Log
	 */
	public function get_email_log(): Email_Log {
		return $this->email_log;
	}

	/**
	 * Getter for Email_Events.
	 *
	 * @return Email_Events
	 */
	public function get_email_events(): Email_Events {
		return $this->email_events;
	}

	/**
	 * Getter for Email_Queue.
	 *
	 * @return Email_Queue
	 */
	public function get_email_queue(): Email_Queue {
		return $this->email_queue;
	}

	/**
	 * Getter for Notices.
	 *
	 * @return Notices
	 */
	public function get_notices(): Notices {
		return $this->notices;
	}

	/**
	 * Getter for Health_Report.
	 *
	 * @return Health_Report
	 */
	public function get_health_report(): Health_Report {
		return $this->health_report;
	}

	/**
	 * Getter for Attachments.
	 *
	 * @return Attachments
	 */
	public function get_attachments(): Attachments {
		return $this->attachments;
	}

	/**
	 * Add the plugin settings link to the plugins page.
	 *
	 * @param array  $links The existing settings links.
	 * @param string $file  The file to link.
	 *
	 * @return array
	 */
	public function plugin_actions_settings_link( array $links, string $file ): array {
		$url           = $this->get_plugin_page_url( array(), 'self' );
		$text          = __( 'Settings', 'wp-offload-ses' );
		$settings_link = '<a href="' . $url . '">' . esc_html( $text ) . '</a>';

		if ( is_multisite() && ! is_network_admin() && ! $this->settings->get_setting( 'enable-subsite-settings' ) ) {
			return $links;
		}

		if ( $file === $this->plugin_basename ) {
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Display the main settings page for the plugin
	 */
	public function render_page() {
		$this->render_view(
			'header',
			array( 'page_title' => $this->get_plugin_page_title(), 'page' => 'wp-offload-ses' )
		);
		$this->render_view( 'settings-tabs' );
		do_action( 'wposes_pre_settings_render' );
		$this->render_view( 'settings' );
		do_action( 'wposes_post_settings_render' );
		$this->render_view( 'footer' );
	}

	/**
	 * Get the tabs available for the plugin settings page
	 *
	 * @return array
	 */
	public function get_settings_tabs(): array {
		$tabs = array();

		if ( $this->is_plugin_setup() ) {
			$tabs['reports']  = _x( 'Reports', 'Show the reports tab', 'wp-offload-ses' );
			$tabs['activity'] = _x( 'Activity', 'Show the activity tab', 'wp-offload-ses' );
			$tabs['settings'] = _x( 'Settings', 'Show the settings tab', 'wp-offload-ses' );
		} else {
			$tabs['start'] = _x( 'Setup', 'Show the setup wizard', 'wp-offload-ses' );
		}

		if ( is_super_admin() ) {
			$tabs['support'] = _x( 'Support', 'Show the support tab', 'wp-offload-ses' );
		}

		return apply_filters( 'wposes_settings_tabs', $tabs );
	}

	/**
	 * Gets the tabs available for the sub-nav
	 *
	 * @return array
	 */
	public function get_settings_sub_nav_tabs(): array {
		$tabs               = array();
		$hide_senders       = Utils::get_first_defined_constant( array(
			'WP_SES_HIDE_VERIFIED',
			'WPOSES_HIDE_VERIFIED',
		) );
		$show_settings_tabs = $this->show_settings_tabs();

		if ( $hide_senders && constant( $hide_senders ) ) {
			$hide_senders = true;
		} else {
			$hide_senders = false;
		}

		if ( is_multisite() && ! is_network_admin() ) {
			$tabs['network-settings'] = _x( 'Network Settings', 'Show the network settings tab', 'wp-offload-ses' );
		}

		if ( $show_settings_tabs ) {
			$tabs['general'] = _x( 'General', 'Show the general settings tab', 'wp-offload-ses' );

			if ( ! $hide_senders ) {
				$tabs['verified-senders'] = _x( 'Verified Senders', 'Show the verified senders tab', 'wp-offload-ses' );
			}
		}

		$tabs['send-test-email'] = _x( 'Send Test Email', 'Show the send test email tab', 'wp-offload-ses' );

		if ( $show_settings_tabs ) {
			$tabs['aws-access-keys'] = _x( 'AWS Access Keys', 'Show the AWS access keys tab', 'wp-offload-ses' );
		}

		return apply_filters( 'wposes_settings_sub_nav_tabs', $tabs );
	}

	/**
	 * Should we display the settings tabs?
	 *
	 * @return bool
	 */
	public function show_settings_tabs(): bool {
		if ( ! is_multisite() || is_network_admin() ) {
			return true;
		}

		if ( $this->settings->get_setting( 'override-network-settings' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Gets the title of the plugin
	 *
	 * @return string
	 */
	public function get_plugin_page_title(): string {
		$title = __( 'Offload SES Lite', 'wp-offload-ses' );

		if ( $this->is_pro() ) {
			$title = __( 'Offload SES', 'wp-offload-ses' );
		}

		return $title;
	}

	/**
	 * Gets the plugin prefix
	 *
	 * @return string
	 */
	public function get_plugin_prefix(): string {
		return $this->plugin_prefix;
	}

	/**
	 * Get the plugin prefix in slug format, ie. replace underscores with hyphens
	 *
	 * @return string
	 */
	public function get_plugin_prefix_slug(): string {
		return str_replace( '_', '-', $this->plugin_prefix );
	}

	/**
	 * Get the nonce key for the settings form of the plugin
	 *
	 * @return string
	 */
	public function get_settings_nonce_key(): string {
		return $this->get_plugin_prefix_slug() . '-save-settings';
	}

	/**
	 * Get an array of verified senders without hitting the API if possible.
	 *
	 * @param bool $force Get them directly from the SES API.
	 *
	 * @return array|Error
	 */
	public function get_verified_senders( bool $force = false ) {
		$verified_senders = get_transient( 'wposes_verified_senders' );

		// Only get identities if not cached, forcing refresh, or cache doesn't match expected format.
		if ( false === $verified_senders || $force || ! is_array( $verified_senders ) || empty( $verified_senders[0]['IdentityName'] ) ) {
			$verified_senders = $this->get_aws()->needs_access_keys() ? array() : $this->get_ses_api()->get_identities();
			set_transient( 'wposes_verified_senders', $verified_senders, 300 );
		}

		return $verified_senders;
	}

	/**
	 * Gets the i18n friendly status of an email.
	 *
	 * @param string $status The email status.
	 *
	 * @return string
	 */
	public function get_email_status_i18n( string $status ): string {
		switch ( $status ) {
			case 'sent':
				$i18n = __( 'Sent', 'wp-offload-ses' );
				break;
			case 'queued':
				$i18n = __( 'Queued', 'wp-offload-ses' );
				break;
			case 'failed':
				$i18n = __( 'Failed', 'wp-offload-ses' );
				break;
			case 'cancelled':
				$i18n = __( 'Cancelled', 'wp-offload-ses' );
				break;
			default:
				$i18n = $status;
		}

		return $i18n;
	}

	/**
	 * Get allowed email statuses.
	 *
	 * @return string[]
	 */
	protected function get_email_statuses(): array {
		return array(
			'sent',
			'cancelled',
			'failed',
			'queued',
		);
	}

	/**
	 * Get email status options for managing emails.
	 *
	 * @return array
	 */
	public function get_email_status_options(): array {
		$options = array(
			'_'   => __( '-- Please Select --', 'wp-offload-ses' ),
			'all' => __( 'All (fast)', 'wp-offload-ses' ),
		);

		if ( is_multisite() && ! is_network_admin() ) {
			$options['all'] = __( 'All', 'wp-offload-ses' );
		}

		foreach ( $this->get_email_statuses() as $status ) {
			$options[ $status ] = $this->get_email_status_i18n( $status );
		}

		$options['not_queued'] = __( 'Not Queued', 'wp-offload-ses' );

		return $options;
	}

	/**
	 * Returns the action links for a provided email status.
	 *
	 * @param int    $id     The ID of the email.
	 * @param string $status The status of the email.
	 *
	 * @return array
	 */
	public function get_email_action_links( int $id, string $status ): array {
		$actions  = array();
		$disabled = $this->is_pro() ? '' : ' disabled';

		$actions['view'] = '<a class="wposes-view-email' . $disabled . '" data-email="' . $id . '" href="#activity">' . __(
				'View Email',
				'wp-offload-ses'
			) . '</a>';

		switch ( $status ) {
			case 'sent':
				$actions['resend'] = '<a class="wposes-resend-email' . $disabled . '" data-email="' . $id . '" href="#activity">' . __(
						'Resend',
						'wp-offload-ses'
					) . '</a>';
				break;
			case 'queued':
				$actions['cancel'] = '<a class="wposes-cancel-email' . $disabled . '" data-email="' . $id . '" href="#activity">' . __(
						'Cancel',
						'wp-offload-ses'
					) . '</a>';
				break;
			case 'failed':
				$actions['retry'] = '<a class="wposes-resend-email' . $disabled . '" data-email="' . $id . '" href="#activity">' . __(
						'Retry',
						'wp-offload-ses'
					) . '</a>';
				break;
			case 'cancelled':
				$actions['send'] = '<a class="wposes-resend-email' . $disabled . '" data-email="' . $id . '" href="#activity">' . __(
						'Send',
						'wp-offload-ses'
					) . '</a>';
				break;
		}

		$actions['delete'] = '<a class="wposes-delete-email' . $disabled . '" data-email="' . $id . '" href="#activity">' . __(
				'Delete Permanently',
				'wp-offload-ses'
			) . '</a>';

		return $actions;
	}

	/**
	 * Check if the provided email address is a verified sender.
	 *
	 * @param string $email       The email address to check.
	 * @param bool   $exact_match Optionally check for exact match only, default false.
	 *
	 * @return bool
	 */
	public function is_verified_email_address( string $email, bool $exact_match = false ): bool {
		$verified_senders = $this->get_verified_senders();

		if ( is_wp_error( $verified_senders ) || ! is_array( $verified_senders ) ) {
			return false;
		}

		foreach ( $verified_senders as $sender ) {
			// Exact match, e.g. most likely an email address.
			if ( $email === $sender['IdentityName'] ) {
				return true;
			}

			// Ends with verified identity name, most likely a domain.
			if ( ! $exact_match && $sender['IdentityName'] === substr( $email, -strlen( $sender['IdentityName'] ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check whether this is the free or pro version.
	 *
	 * @return bool
	 */
	public function is_pro(): bool {
		return false;
	}

	/**
	 * Check if the plugin is already set up.
	 *
	 * @return bool
	 */
	public function is_plugin_setup(): bool {
		$is_setup = true;

		if ( false === $this->settings->get_setting( 'completed-setup', false ) ) {
			$is_setup = false;
		}

		// For subsites, we show the Network Settings tab by default.
		if ( is_multisite() && ! is_network_admin() ) {
			$is_setup = true;
		}

		if ( isset( $_GET['setup-wizard'] ) ) { // phpcs:ignore
			$is_setup = false;
		}

		if ( isset( $_GET['skip-setup'] ) ) { // phpcs:ignore
			$is_setup = true;
		}

		return apply_filters( 'wposes_is_plugin_setup', $is_setup );
	}

	/**
	 * AJAX handler for get_diagnostic_info()
	 */
	public function ajax_get_diagnostic_info() {
		$this->verify_ajax_request();

		$diagnostic_info = new Diagnostic_Info();

		$out = array(
			'success'         => '1',
			'diagnostic_info' => $diagnostic_info->output_diagnostic_info(),
		);

		$this->end_ajax( $out );
	}

	/**
	 * Set AWS keys via ajax.
	 */
	public function ajax_set_aws_keys() {
		check_ajax_referer( 'wposes-aws-keys-set' );

		$key_id     = filter_input( INPUT_POST, 'aws-access-key-id' );
		$secret_key = filter_input( INPUT_POST, 'aws-secret-access-key' );
		$response   = array(
			'message' => __( 'Access keys updated successfully.', 'wp-offload-ses' ),
		);

		if ( Amazon_Web_Services::is_any_access_key_constant_defined() ) {
			wp_send_json_error(
				array(
					'message'             => __(
						'All access key constants must be removed before keys can be set in the database.',
						'wp-offload-ses'
					),
					'access_keys_defined' => true,
				)
			);
		}

		if ( $key_id ) {
			$this->settings->set_setting( 'aws-access-key-id', $key_id );
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'The Access Key ID must be set.', 'wp-offload-ses' ),
				)
			);
		}

		// Only update the secret key if entered and not set to "-- not shown --".
		if (
			_x(
				'-- not shown --',
				'placeholder for hidden access key, 39 char max',
				'wp-offload-ses'
			) !== $secret_key ||
			! $this->settings->get_setting( 'aws-secret-access-key' )
		) {
			// AWS Secret Access keys are 40 char long.
			if ( ! $secret_key || strlen( $secret_key ) < 40 ) {
				wp_send_json_error(
					array(
						'message' => __(
							'The Secret Access Key must be at least 40 characters long.',
							'wp-offload-ses'
						),
					)
				);
			}

			$this->settings->set_setting( 'aws-secret-access-key', $secret_key );
		}

		if ( ! $this->get_ses_api()->check_access_keys( true ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'The provided access keys are invalid.', 'wp-offload-ses' ),
				)
			);
		}

		$this->settings->save_settings();

		wp_send_json_success( $response );
	}

	/**
	 * Remove AWS access keys via ajax.
	 */
	public function ajax_remove_aws_keys() {
		check_ajax_referer( 'wposes-aws-keys-remove' );

		$this->remove_aws_keys();

		wp_send_json_success(
			array(
				'message' => __( 'Access keys removed from the database successfully.', 'wp-offload-ses' ),
			)
		);
	}

	/**
	 * Remove AWS access keys from saved settings if a key constant is defined.
	 */
	public function remove_aws_keys_if_constants_set() {
		if ( Amazon_Web_Services::is_any_access_key_constant_defined() ) {
			$this->remove_aws_keys();
		}
	}

	/**
	 * Remove AWS keys from settings.
	 */
	protected function remove_aws_keys() {
		$this->settings->remove_setting( 'aws-access-key-id' );
		$this->settings->remove_setting( 'aws-secret-access-key' );
		$this->settings->save_settings();
	}

	/**
	 * Save settings over AJAX.
	 */
	public function ajax_save_settings() {
		$this->verify_ajax_request();

		$response  = array();
		$post_vars = $this->settings->get_settings_whitelist();
		parse_str( $_POST['settings'], $settings ); // phpcs:ignore

		foreach ( $post_vars as $var ) {
			if ( ! isset( $settings[ $var ] ) ) { // input var okay.
				continue;
			}

			$value = $this->settings->sanitize_setting( $var, $settings[ $var ] );

			$this->settings->set_setting( $var, $value );
		}

		$this->settings->save_settings();

		if ( isset( $settings['region'] ) ) {
			// Get the updated verified senders.
			$response['verified_senders'] = $this->get_verified_senders( true );
		}

		wp_send_json_success( $response );
	}

	/**
	 * Display the activity table over AJAX.
	 */
	public function ajax_activity_table() {
		$activity_table = new Activity_List_Table();
		// TODO: Remove or find actual function to call - $activity_table->load();
		$activity_table->ajax_response();
	}

	/**
	 * Verify a new sender with SES.
	 */
	public function ajax_verify_sender() {
		$this->verify_ajax_request();

		$sender      = filter_input( INPUT_POST, 'sender' );
		$sender_type = filter_input( INPUT_POST, 'sender_type' );

		// Since SESv2, verify is no longer idempotent,
		// so we'll try and delete an existing email identity first.
		if ( $this->is_verified_email_address( $sender, true ) ) {
			$this->get_ses_api()->delete_identity( $sender );
		}

		if ( 'domain' === $sender_type ) {
			$response = $this->get_ses_api()->verify_domain( $sender );
		} else {
			$response = $this->get_ses_api()->verify_email_address( $sender );
		}

		delete_transient( 'wposes_verified_senders' );

		$this->end_ajax( $response );
	}

	/**
	 * Delete a sender from SES.
	 */
	public function ajax_delete_sender() {
		check_ajax_referer( 'wposes-verified-senders-nonce', 'wposes_verified_senders_nonce' );

		$sender   = filter_input( INPUT_POST, 'sender' );
		$response = $this->get_ses_api()->delete_identity( $sender );

		delete_transient( 'wposes_verified_senders' );

		$this->end_ajax( $response );
	}

	/**
	 * Display the verified senders table over AJAX.
	 */
	public function ajax_verified_senders_list() {
		$verified_senders_table = new Verified_Senders_List_Table();
		// TODO: Remove or find actual function to call - $verified_senders_table->load();
		$verified_senders_table->ajax_response();
	}

	/**
	 * Send a test email over AJAX.
	 *
	 * @throws phpmailerException
	 */
	public function ajax_send_test_email() {
		$this->verify_ajax_request();

		$current_user = wp_get_current_user();
		$username     = $current_user->display_name;
		$to           = filter_input( INPUT_POST, 'email_address' );
		$subject      = __( 'WP Offload SES Test Email', 'wp-offload-ses' );
		$content      = sprintf(
			__(
				"Hi %s,\n\nAre you seeing this email? You are? Well awesome - that means you're all set to start sending emails from your site via Amazon SES 🎉",
				'wp-offload-ses'
			),
			$username
		);

		$email  = new Email( $to, $subject, $content, '', '' );
		$raw    = $email->prepare();
		$result = $this->get_ses_api()->send_email( $raw );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_data() );
		}

		wp_send_json_success();
	}

	/**
	 * Purge logs for a given email status via AJAX request.
	 */
	public function ajax_purge_logs() {
		$this->verify_ajax_request();

		$email_status = filter_input( INPUT_POST, 'email_status' );

		if ( empty( $email_status ) ) {
			wp_send_json_error(
				__( 'An email status must be supplied.', 'wp-offload-ses' )
			);
		}

		$result = $this->email_log->purge_logs( $email_status );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		wp_send_json_success();
	}

	/**
	 * Helper method for verifying an AJAX request.
	 */
	public function verify_ajax_request() {
		if (
			! is_admin() ||
			! wp_verify_nonce( sanitize_key( $_POST['_nonce'] ), sanitize_key( $_POST['action'] ) )
		) { // phpcs:ignore
			wp_die( __( 'Cheatin&#8217; eh?', 'wp-offload-ses' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-offload-ses' ) );
		}
	}

	/**
	 * Helper method for returning data to AJAX call
	 *
	 * @param mixed $return Data to return.
	 */
	public function end_ajax( $return = array() ) {
		wp_send_json( $return );
	}

	/**
	 * Check if any emails were sent with unverified senders.
	 */
	private function check_unverified_senders() {
		if ( ! $this->settings->get_setting( 'send-via-ses' ) ) {
			return;
		}

		$unverified_senders = get_transient( 'wposes_unverified_senders' );

		if ( false === $unverified_senders ) {
			return;
		}

		$message = __(
			'We\'ve noticed that emails are being sent from the following unverified email addresses:',
			'wp-offload-ses'
		);
		$message .= '<ul>';

		foreach ( $unverified_senders as $sender ) {
			$sender  = esc_html( $sender );
			$message .= "<li>{$sender}</li>";
		}

		$message .= '</ul>';
		$message .= sprintf(
			__(
				'Please <a href="%s">verify these email addresses</a> with Amazon, or use an email address that has already been verified.',
				'wp-offload-ses'
			),
			'#verified-senders'
		);

		$args = array(
			'type'                  => 'error',
			'only_show_to_user'     => false,
			'flash'                 => false,
			'remove_on_dismiss'     => true,
			'only_show_in_settings' => true,
			'subsite'               => true,
		);

		$this->get_notices()->add_notice( $message, $args );
		delete_transient( 'wposes_unverified_senders' );
	}

	/**
	 * Check if any defined access keys are valid and display a notice if they are not.
	 */
	private function check_defined_access_keys() {
		if ( Amazon_Web_Services::is_any_access_key_constant_defined() && ! $this->get_ses_api()->check_access_keys() ) {
			$message = __(
				'Your AWS Access Keys are invalid, please check the credentials you have defined in your wp-config.php and refresh this page.',
				'wp-offload-ses'
			);
			$args    = array(
				'type'                  => 'error',
				'only_show_to_user'     => false,
				'flash'                 => true,
				'only_show_in_settings' => true,
				'subsite'               => true,
			);

			$this->get_notices()->add_notice( $message, $args );
		}
	}

	/**
	 * Polyfill for displaying "Settings saved." consistently between single-site and multisite environments.
	 *
	 * TL;DR: options-head.php is loaded for options-general.php (single sites only) which does this, but not on multisite.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/c2d709e9d6cbe7f9b3c37da0a7c9aae788158124/wp-admin/admin-header.php#L265-L266
	 * @see https://github.com/WordPress/WordPress/blob/9b68e5953406024c75b92f7ebe2aef0385c8956e/wp-admin/options-head.php#L13-L16
	 */
	public function settings_saved_notice() {
		if ( isset( $_GET['updated'] ) && isset( $_GET['page'] ) ) {
			// For back-compat with plugins that don't use the Settings API and just set updated=1 in the redirect.
			add_settings_error( 'general', 'settings_updated', __( 'Settings saved.' ), 'updated' );
		}
		settings_errors();
	}

	/**
	 * Handle the "Override Network Settings" option.
	 */
	public function maybe_override_network_settings() {
		if ( empty( $_POST['plugin'] ) || $this->get_plugin_slug() != sanitize_key( $_POST['plugin'] ) ) { // input var okay
			return;
		}

		if ( empty( $_POST['action'] ) || 'save_override_network_settings' != sanitize_key( $_POST['action'] ) ) { // input var okay
			return;
		}

		if (
			! wp_verify_nonce(
				sanitize_key( $_POST['wposes_override_network_settings'] ),
				$this->get_settings_nonce_key()
			)
		) { // input var okay
			die( __( "Cheatin' eh?", 'wp-offload-ses' ) );
		}

		if ( $_POST['override-network-settings'] ) {
			$this->settings->set_setting( 'override-network-settings', true );
		} else {
			$this->settings->set_settings( array( 'override-network-settings' => false ) );
		}

		$this->settings->save_settings();

		$url = $this->get_plugin_page_url( array( 'updated' => '1', 'hash' => 'network-settings' ), 'self' );
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Handle the saving of the settings page
	 */
	public function handle_post_request() {
		if ( empty( $_POST['plugin'] ) || $this->get_plugin_slug() != sanitize_key( $_POST['plugin'] ) ) { // input var okay
			return;
		}

		if ( empty( $_POST['action'] ) || 'save' != sanitize_key( $_POST['action'] ) ) { // input var okay
			return;
		}

		if (
			! wp_verify_nonce(
				sanitize_key( $_POST['wposes_save_settings'] ),
				$this->get_settings_nonce_key()
			)
		) { // input var okay
			die( __( "Cheatin' eh?", 'wp-offload-ses' ) );
		}

		do_action( 'wposes_pre_save_settings' );

		$post_vars = $this->settings->get_settings_whitelist();

		foreach ( $post_vars as $var ) {
			if ( 'override-network-settings' !== $var ) {
				$this->settings->remove_setting( $var );
			}

			if ( ! isset( $_POST[ $var ] ) ) { // input var okay
				continue;
			}

			$value = $this->settings->sanitize_setting( $var, $_POST[ $var ] );

			$this->settings->set_setting( $var, $value );
		}

		$this->settings->save_settings();

		$url = $this->get_plugin_page_url( array( 'updated' => '1' ), 'self' );
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Check for wposes-download-log and related nonce and if found begin the
	 * download of the diagnostic log
	 *
	 * @return void
	 */
	public function http_prepare_download_log() {
		if (
			isset( $_GET['wposes-download-log'] ) &&
			wp_verify_nonce( $_GET['nonce'], 'wposes-download-log' )
		) { // phpcs:ignore
			$diagnostic_info = new Diagnostic_Info();
			$log             = $diagnostic_info->output_diagnostic_info( false );
			$url             = parse_url( home_url() );
			$host            = sanitize_file_name( $url['host'] );
			$filename        = sprintf( '%s-diagnostic-log-%s.txt', $host, date( 'YmdHis' ) );
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Length: ' . strlen( $log ) );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			echo $log;
			exit;
		}
	}

	/**
	 * More info link.
	 *
	 * @param string $path        URL path.
	 * @param string $utm_content UTM tags.
	 * @param string $hash        URL hash.
	 *
	 * @return string
	 */
	public function more_info_link( string $path, string $utm_content = '', string $hash = '' ): string {
		$args = array(
			'utm_campaign' => 'support+docs',
		);

		if ( ! empty( $utm_content ) ) {
			$args['utm_content'] = $utm_content;
		}

		$url  = $this->dbrains_url( $path, $args, $hash );
		$text = __( 'More&nbsp;info&nbsp;&raquo;', 'wp-offload-ses' );
		$link = Utils::dbrains_link( $url, $text );

		return sprintf( '<span class="more-info">%s</span>', $link );
	}

	/**
	 * Maybe decode the subject line.
	 *
	 * @param string $subject The email subject.
	 *
	 * @return string|bool
	 */
	public function maybe_decode_subject( string $subject ) {
		if (
			'=?' === substr( $subject, 0, 2 ) &&
			'?=' === substr( $subject, -2 ) &&
			function_exists( 'mb_decode_mimeheader' )
		) {
			return mb_decode_mimeheader( $subject );
		}

		return $subject;
	}

	/**
	 * Adds a notice that an email has failed.
	 */
	public function add_failed_email_notice() {
		$url_method = 'self';
		if ( is_multisite() && ! $this->settings->get_setting( 'enable-subsite-settings', false ) ) {
			$url_method = 'network';
		}

		$message = sprintf(
			__(
				'<strong>WP Offload SES &mdash; </strong> One or more emails have failed to send. <a href="%s">View failures in the Activity tab</a> or <a href="%s">check out our doc for debugging failed emails</a>.',
				'wp-offload-ses'
			),
			$this->get_plugin_page_url(
				array(
					'status' => 'failed',
					'hash'   => 'activity',
				),
				$url_method
			),
			$this->dbrains_url(
				'/wp-offload-ses/doc/general-debugging/',
				array(
					'utm_campaign' => 'error+messages',
				)
			)
		);

		$args = array(
			'type'              => 'error',
			'only_show_to_user' => false,
			'flash'             => false,
			'remove_on_dismiss' => true,
			'subsite'           => true,
		);

		$this->get_notices()->add_notice( $message, $args );
	}

	/**
	 * Mail handler
	 *
	 * @param string|array $to          The email recipient.
	 * @param string       $subject     The email subject.
	 * @param string       $message     The email message.
	 * @param string|array $headers     The email headers.
	 * @param string|array $attachments The email attachments.
	 *
	 * @return bool
	 */
	public function mail_handler( $to, $subject, $message, $headers, $attachments ): bool {
		$content_type = apply_filters( 'wp_mail_content_type', 'text/plain' );

		// Add Content-Type header now in case filter is removed by time queue is run.
		if ( 'text/html' === $content_type ) {
			if ( is_array( $headers ) ) {
				$headers[] = 'Content-Type: text/html;';
			} else {
				$headers = "Content-Type: text/html;\n" . $headers;
			}
		}

		$subject = $this->maybe_decode_subject( $subject );

		/**
		 * Alter an email's attributes before it is added to the queue to be sent.
		 * Returning an empty array will stop the email from being queued and sent.
		 *
		 * @param array $atts with keys 'to', 'subject', 'message', 'headers' and 'attachments'.
		 *
		 * @return array
		 */
		$atts     = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );
		$email_id = $this->get_email_log()->log_email( $atts );

		if ( false === $email_id ) {
			return false;
		}

		if ( ! is_array( $attachments ) ) {
			$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
		}

		foreach ( $attachments as $attachment ) {
			$this->get_attachments()->handle_attachment( $email_id, $attachment );
		}

		$this->get_email_queue()->process_email( $email_id );

		if ( ! $this->settings->get_setting( 'enqueue-only', false ) ) {
			$this->trigger_queue();
		}

		return true;
	}

	/**
	 * Sends an async request to trigger the queue if possible.
	 *
	 * @return bool
	 */
	public function trigger_queue(): bool {
		// Make sure we're not DDOSing our own site.
		if ( get_transient( 'wposes_triggered_queue' ) ) {
			return false;
		}

		set_transient( 'wposes_triggered_queue', true, 10 );

		$data = array(
			'action' => 'wposes_trigger_queue',
			'nonce'  => wp_create_nonce( 'wposes_trigger_queue' ),
		);

		$request_args = apply_filters(
			'wposes_queue_request',
			array(
				'url'  => admin_url( 'admin-ajax.php' ),
				'args' => array(
					'timeout'   => 0.01,
					'blocking'  => false,
					'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
					'body'      => $data,
					'cookies'   => $_COOKIE,
				),
			)
		);

		$result = wp_remote_post( $request_args['url'], $request_args['args'] );

		return ! is_wp_error( $result );
	}

	/**
	 * Delete an email with the provided email id.
	 *
	 * @param int $email_id The ID of the email to cancel.
	 *
	 * @return bool
	 */
	public function delete_email( int $email_id ): bool {
		if ( ! $email_id ) {
			return false;
		}

		// Cancel the email and remove click events if necessary.
		$this->get_email_queue()->cancel_email( $email_id );
		$this->get_email_events()->delete_links_by_email( $email_id );
		$this->get_attachments()->delete_email_attachments( $email_id );

		// Delete the actual email.
		$delete = $this->get_email_log()->delete_email( $email_id );

		if ( ! $delete ) {
			return false;
		}

		return true;
	}

	/**
	 * Maybe delete the successfully sent email.
	 *
	 * @param string $email_to
	 * @param string $email_subject
	 * @param string $email_message
	 * @param array  $email_headers
	 * @param array  $email_attachments
	 * @param int    $email_id
	 *
	 * @return void
	 *
	 * @handles wposes_mail_sent
	 */
	public function maybe_delete_successful(
		$email_to,
		$email_subject,
		$email_message,
		$email_headers,
		$email_attachments,
		$email_id
	) {
		if (
			empty( $email_id ) ||
			! is_int( $email_id ) ||
			! $this->settings->get_setting( 'delete-successful', false )
		) {
			return;
		}

		$this->delete_email( $email_id );
	}

	/**
	 * Get UTM source for plugin.
	 *
	 * @return string
	 */
	protected static function get_utm_source() {
		return 'SES+Free';
	}

	/**
	 * Get UTM content for WP Engine URL.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	protected static function get_wpe_url_utm_content( $content = 'plugin_footer_text' ) {
		return 'oses_free_' . $content;
	}
}
