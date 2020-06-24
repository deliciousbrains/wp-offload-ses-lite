<?php
/**
 * Email Health Report for WP Offload SES.
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\Email;
use DeliciousBrains\WP_Offload_SES\SES_API;

/**
 * Class Pro_Health_Report.
 *
 * @since 1.4.0
 */
class Health_Report {

	/**
	 * Stores the ID used for cron events.
	 *
	 * @var string
	 */
	protected $cron_id;

	/**
	 * Stores the WordPress database class.
	 *
	 * @var \WPDB
	 */
	protected $database;

	/**
	 * Stores the main plugin class.
	 *
	 * @var WP_Offload_SES
	 */
	protected $wposes;

	/**
	 * The most records we should show in a table.
	 *
	 * @var int
	 */
	protected $table_limit = 5;

	/**
	 * The table used to store email logs.
	 *
	 * @var string
	 */
	protected $emails_table;

	/**
	 * The table used to store click data.
	 *
	 * @var string
	 */
	protected $clicks_table;

	/**
	 * Whether this is a network-level report.
	 *
	 * @var bool
	 */
	protected $is_network_report = false;

	/**
	 * Constructs the Health_Report class.
	 *
	 * @param WP_Offload_SES $wp_offload_ses The main WP Offload SES class.
	 */
	public function __construct( WP_Offload_SES $wp_offload_ses ) {
		global $wpdb;

		$this->cron_id         = 'deliciousbrains_wp_offload_ses_health_report';
		$this->network_cron_id = 'deliciousbrains_wp_offload_ses_network_health_report';
		$this->database        = $wpdb;
		$this->wposes          = $wp_offload_ses;
		$this->emails_table    = $this->database->base_prefix . 'oses_emails';
		$this->clicks_table    = $this->database->base_prefix . 'oses_clicks';

		$this->init( $this->cron_id );

		if ( is_multisite() && is_main_site() ) {
			$this->set_network_report();
			$this->init( $this->network_cron_id );
		}
	}

	/**
	 * Initiates the cron for the health report.
	 *
	 * @param string $cron The cron to use.
	 *
	 * @return bool
	 */
	public function init( $cron ) {
		if ( ! $this->is_enabled() ) {
			wp_clear_scheduled_hook( $cron, array( $this->is_network_report ) );
			return false;
		}

		$send_time      = $this->get_next_send_time();
		$next_scheduled = wp_next_scheduled( $cron, array( $this->is_network_report ) );

		// Invalid send time.
		if ( ! $send_time ) {
			wp_clear_scheduled_hook( $cron, array( $this->is_network_report ) );
			return false;
		}

		// Not scheduled yet.
		if ( ! $next_scheduled ) {
			wp_schedule_single_event( $send_time, $cron, array( $this->is_network_report ) );
			return true;
		}

		$current_date        = (int) date( 'z', current_time( 'timestamp', 1 ) );
		$next_scheduled_date = (int) date( 'z', $next_scheduled );
		$expected_time       = $send_time;

		if ( $next_scheduled_date === $current_date ) {
			$expected_time = $this->get_next_send_time( '-1 day' );
		}

		// Frequency has changed, schedule a new cron.
		if ( $next_scheduled !== $expected_time ) {
			wp_clear_scheduled_hook( $cron, array( $this->is_network_report ) );
			wp_schedule_single_event( $send_time, $cron, array( $this->is_network_report ) );
		}

		add_action( $cron, array( $this, 'send' ), 10, 1 );

		return true;
	}

	/**
	 * Returns a timestamp of the next time the report should send.
	 *
	 * @param string $offset Offsets the current time.
	 *
	 * @return int|bool
	 */
	public function get_next_send_time( $offset = '' ) {
		$frequency = $this->get_frequency();
		$time      = current_time( 'mysql' );
		$format    = 'Y-m-d H:i:s';

		if ( '' !== $offset ) {
			$date   = \DateTime::createFromFormat( $format, $time );
			$offset = $date->modify( $offset );
			$time   = $date->format( $format );
		}

		switch ( $frequency ) {
			case 'monthly':
				$date        = \DateTime::createFromFormat( $format, $time );
				$day_to_send = $date->modify( 'first day of next month' );
				break;
			case 'daily':
				$date        = \DateTime::createFromFormat( $format, $time );
				$day_to_send = $date->modify( '+1 day' );
				break;
			case 'weekly':
			default:
				$week_start_end = get_weekstartend( $time );
				$week_end       = new \DateTime( '@' . $week_start_end['end'] );
				$day_to_send    = $week_end->modify( '+1 day' );
				break;
		}

		$send_time = apply_filters( 'wposes_health_report_send_time', '8am' );
		$send_time = $day_to_send->modify( $send_time );

		if ( ! $send_time ) {
			return false;
		}

		/**
		 * WordPress cron always runs on GMT, so we need to convert
		 * our calculated local time into a GMT timestamp.
		 */
		$timestamp = get_gmt_from_date( $send_time->format( $format ), 'U' );

		return (int) $timestamp;
	}

	/**
	 * Checks if the health report has been enabled.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$is_enabled = $this->wposes->settings->get_setting( 'enable-health-report', false );

		if ( ! is_multisite() ) {
			return $is_enabled;
		}

		$subsite_settings_enabled    = $this->wposes->settings->get_setting( 'enable-subsite-settings', false );
		$overriding_network_settings = $this->wposes->settings->get_setting( 'override-network-settings', false );
		$is_network_enabled          = $this->wposes->settings->get_network_setting( 'enable-health-report', false );

		// Just checking if it's been network enabled.
		if ( $this->is_network_report() ) {
			return $is_network_enabled;
		}

		// Subsite can't override.
		if ( ! $subsite_settings_enabled ) {
			return false;
		}

		// Subsite isn't overriding.
		if ( $subsite_settings_enabled && ! $overriding_network_settings ) {
			return $is_network_enabled;
		}

		// Subsite is overriding, OK to use subsite settings.
		if ( $subsite_settings_enabled && $overriding_network_settings ) {
			return $is_enabled;
		}

		return false;
	}

	/**
	 * Sets the report to be a network report.
	 *
	 * @param bool $network If this is a network report.
	 */
	public function set_network_report( $network = true ) {
		$this->is_network_report = (bool) $network;
	}

	/**
	 * Checks if the report is a network report.
	 *
	 * @return bool
	 */
	public function is_network_report() {
		return (bool) $this->is_network_report;
	}

	/**
	 * Checks if the report should be sent out at the subsite level.
	 *
	 * @return bool
	 */
	public function is_subsite_report() {
		if ( ! is_multisite() ) {
			return false;
		}

		return ! $this->is_network_report();
	}

	/**
	 * Sends the report.
	 *
	 * @param bool $network If this is a network report.
	 * 
	 * @return bool
	 */
	public function send( $network = false ) {
		$this->set_network_report( $network );

		if ( ! $this->should_send() ) {
			return false;
		}

		ob_start();
		$this->wposes->render_view( 'health-report' );
		$content = ob_get_clean();

		$subject    = $this->get_report_subject();
		$recipients = $this->get_recipients();
		$headers    = array( 'Content-Type: text/html' );

		$email  = new Email( $recipients, $subject, $content, $headers, array() );
		$raw    = $email->prepare();
		$result = $this->wposes->get_ses_api()->send_email( $raw );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if we should send out the health report.
	 *
	 * @return bool
	 */
	public function should_send() {
		if ( ! $this->wposes->settings->get_setting( 'send-via-ses' ) || ! $this->is_enabled() ) {
			return false;
		}

		return true;
	}

	/**
	 * Gets the available report frequencies.
	 *
	 * @return array
	 */
	public function get_available_frequencies() {
		return array(
			'daily'   => __( 'Daily (upgrade required)', 'wp-offload-ses' ),
			'weekly'  => __( 'Weekly', 'wp-offload-ses' ),
			'monthly' => __( 'Monthly', 'wp-offload-ses' ),
		);
	}

	/**
	 * Gets the available report recipients.
	 *
	 * @return array
	 */
	public function get_available_recipients() {
		return array(
			'site-admins' => __( 'Site Admins', 'wp-offload-ses' ),
			'custom'      => __( 'Custom (upgrade required)', 'wp-offload-ses' ),
		);
	}

	/**
	 * Gets the translated length of the reporting period.
	 *
	 * @return string
	 */
	public function get_reporting_period() {
		$frequency = $this->get_frequency();
		$text      = array(
			'daily'   => __( 'day', 'wp-offload-ses' ),
			'weekly'  => __( 'week', 'wp-offload-ses' ),
			'monthly' => __( 'month', 'wp-offload-ses' ),
		);

		if ( isset( $text[ $frequency ] ) ) {
			return $text[ $frequency ];
		}

		return __( 'reporting period', 'wp-offload-ses' );
	}

	/**
	 * Gets the frequency that the report should be sent out at.
	 *
	 * @return string
	 */
	public function get_frequency() {
		if ( $this->is_network_report() ) {
			return $this->wposes->settings->get_network_setting( 'health-report-frequency', 'weekly' );
		}

		return $this->wposes->settings->get_setting( 'health-report-frequency', 'weekly' );
	}

	/**
	 * Gets the recipients of the report.
	 *
	 * @return array
	 */
	public function get_recipients() {
		if ( $this->is_network_report() ) {
			$admins      = array();
			$site_admins = get_site_option( 'site_admins' );
			foreach ( $site_admins as $admin ) {
				$admins[] = get_user_by( 'slug', $admin );
			}
		} else {
			$recipients = array();
			$admins     = get_users( 'role=Administrator' );	
		}

		foreach ( $admins as $admin ) {
			$recipients[] = $admin->user_email;
		}

		return $recipients;
	}

	/**
	 * Gets the report start date.
	 *
	 * @return string
	 */
	public function get_report_start_date() {
		$frequency  = $this->get_frequency();
		$end_date   = $this->get_report_end_date();
		$start_date = new \Datetime( $end_date );

		switch( $frequency ) {
			case 'daily':
				$start_date = $start_date;
				break;
			case 'monthly':
				$start_date = $start_date->modify( 'first day of this month' );
				break;
			case 'weekly':
			default:
				$start_date = $start_date->modify( '-6 days' );
				break;
		}

		return $start_date->format( 'Y-m-d 00:00:00' );
	}

	/**
	 * Gets the report end date.
	 *
	 * @return string
	 */
	public function get_report_end_date() {
		$format = 'Y-m-d H:i:s';
		$date   = \DateTime::createFromFormat( $format, current_time( 'mysql' ) );

		$date->modify( '-1 day' );

		return $date->format( 'Y-m-d 23:59:59' );
	}

	/**
	 * Gets the start of the week.
	 *
	 * @return int
	 */
	public function get_week_start() {
		return get_option( 'start_of_week', 1 );
	}

	/**
	 * Get plugin name.
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return __( 'WP Offload SES Lite', 'wp-offload-ses' );
	}

	/**
	 * Get the plugin logo used in the email header/footer.
	 *
	 * @return string
	 */
	public function get_plugin_logo() {
		$src = $this->wposes->plugins_url( 'assets/img/SES-circle.png' );
		$src = apply_filters( 'as3cf_get_asset', $src );
		return '<img width="50" alt="" style="width: 50px; display: block;" src="' . esc_url( $src ) . '" />';
	}

	/**
	 * Gets the "View Full Report" URL.
	 *
	 * @param string $which The type of report to get.
	 *
	 * @return string
	 */
	public function get_view_full_report_link( $which ) {
		$args = array(
			'hash'   => 'activity',
			'status' => 'sent' === $which ? 'sent' : 'failed'
		);

		$method = $this->is_subsite_report() ? 'self' : 'network';

		return sprintf(
			'<a href="%1$s" style="float: right;">%2$s</a>',
			esc_url( $this->wposes->get_plugin_page_url( $args, $method ) ),
			__( 'View Full Report', 'wp-offload-ses' )
		);
	}

	/**
	 * Gets the subject for the report.
	 *
	 * @return string
	 */
	public function get_report_subject() {
		$domain = Utils::current_base_domain();

		if ( is_multisite() && ! is_main_site() ) {
			$parts  = parse_url( home_url() );
			$url    = $parts['host'] . ( isset( $parts['path'] ) ? $parts['path'] : '' );
			$domain = untrailingslashit( $url );
		}

		return sprintf(
			__( '[WP Offload SES] %1$s email sending health for %2$s', 'wp-offload-ses' ),
			$this->get_report_date_range(),
			$domain
		);
	}

	/**
	 * Gets the date range used in the email header.
	 *
	 * @return string
	 */
	public function get_report_date_range() {
		$frequency = $this->get_frequency();
		$format    = get_option( 'date_format' );
		$start     = new \DateTime( $this->get_report_start_date() );
		$end       = new \DateTime( $this->get_report_end_date() );

		if ( 'daily' === $frequency ) {
			// Daily frequency doesn't require a range.
			return $start->format( $format );
		}

		$range = sprintf(
			__( '%1$s through %2$s', 'wp-offload-ses' ),
			$start->format( $format ),
			$end->format( $format )
		);

		return $range;
	}

	/**
	 * Gets the total number of different subjects sent
	 * during the report period.
	 *
	 * @return int
	 */
	public function get_total_subjects_sent() {
		$subsite_sql = '';

		if ( $this->is_subsite_report() ) {
			$subsite_id  = (int) get_current_blog_id();
			$subsite_sql = "AND emails.subsite_id = {$subsite_id}";
		}

		$query = $this->database->prepare(
			"SELECT COUNT(DISTINCT(emails.email_subject))
			FROM {$this->emails_table} emails
			WHERE emails.email_status = 'sent'
			{$subsite_sql}
			AND emails.email_created >= %s
			AND emails.email_created <= %s",
			$this->get_report_start_date(),
			$this->get_report_end_date()
		);

		return (int) $this->database->get_var( $query );
	}

	/**
	 * Gets an array of the emails sent, used to populate
	 * the table in the health report.
	 *
	 * @return array
	 */
	public function get_sent_emails() {
		$subsite_sql = '';

		if ( $this->is_subsite_report() ) {
			$subsite_id  = (int) get_current_blog_id();
			$subsite_sql = "AND emails.subsite_id = {$subsite_id}";
		}

		$query = $this->database->prepare(
			"SELECT emails.email_subject AS subject,
			COUNT(DISTINCT emails.email_id) AS emails_sent
			FROM {$this->emails_table} emails
			WHERE emails.email_created >= %s
			AND emails.email_created <= %s
			{$subsite_sql}
			AND emails.email_status = 'sent'
			GROUP BY subject
			ORDER BY emails_sent DESC
			LIMIT {$this->table_limit}",
			$this->get_report_start_date(),
			$this->get_report_end_date()
		);

		$sent_emails = $this->database->get_results( $query, ARRAY_A );

		return array_map(
			function( $value ) {
				$upgrade_url = $this->wposes->dbrains_url(
					'/wp-offload-ses/',
					array(
						'utm_campaign' => 'WP+Offload+SES+Upgrade',
						'utm_content'  => 'health+report',
					)
				);
				$upgrade_link = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $upgrade_url ),
					__( 'Upgrade', 'wp-offload-ses' )
				);

				$value['open_count']  = $upgrade_link;
				$value['click_count'] = $upgrade_link;

				return $value;
			},
			$sent_emails
		);
	}

	/**
	 * Gets the total number of email failures sent
	 * during the report period.
	 *
	 * @return int
	 */
	public function get_total_email_failures() {
		$subsite_sql = '';

		if ( $this->is_subsite_report() ) {
			$subsite_id  = (int) get_current_blog_id();
			$subsite_sql = "AND emails.subsite_id = {$subsite_id}";
		}

		$query = $this->database->prepare(
			"SELECT COUNT(*)
			FROM {$this->emails_table} emails
			WHERE emails.email_status = 'failed'
			{$subsite_sql}
			AND emails.email_created >= %s
			AND emails.email_created <= %s",
			$this->get_report_start_date(),
			$this->get_report_end_date()
		);

		return (int) $this->database->get_var( $query );
	}

	/**
	 * Gets an array of emails that failed,
	 * used to populate the table in the health report.
	 *
	 * @return array
	 */
	public function get_failed_emails() {
		$subsite_sql = '';

		if ( $this->is_subsite_report() ) {
			$subsite_id  = (int) get_current_blog_id();
			$subsite_sql = "AND emails.subsite_id = {$subsite_id}";
		}

		$query = $this->database->prepare(
			"SELECT emails.email_id AS id,
				emails.email_subject AS subject,
				emails.email_created AS date,
				emails.email_to AS recipient
			FROM {$this->emails_table} emails
			WHERE emails.email_created >= %s
			AND emails.email_created <= %s
			AND emails.email_status = 'failed'
			{$subsite_sql}
			LIMIT {$this->table_limit}",
			$this->get_report_start_date(),
			$this->get_report_end_date()
		);

		return $this->database->get_results( $query, ARRAY_A );
	}

}
