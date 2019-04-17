<?php
/**
 * Handles notices for WP Offload SES.
 *
 * @package WP Offload SES
 * @author Delicious Brains
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;

/**
 * Class Notices
 *
 * @since 1.0.0
 */
class Notices {

	/**
	 * Stores an instance of this class.
	 *
	 * @var Notices
	 */
	protected static $instance = null;

	/**
	 * The main WP Offload SES plugin class.
	 *
	 * @var WP_Offload_SES
	 */
	private $wp_offload_ses;

	/**
	 * Get an instance of this class.
	 *
	 * Use this instead of __construct().
	 *
	 * @param WP_Offload_SES $wp_offload_ses The main plugin class.
	 *
	 * @return Notices
	 */
	public static function get_instance( $wp_offload_ses ) {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static( $wp_offload_ses );
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @param WP_Offload_SES $wp_offload_ses The main plugin class.
	 */
	protected function __construct( $wp_offload_ses ) {
		$this->wp_offload_ses = $wp_offload_ses;

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'network_admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'wposes_pre_tab_render', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_notice_scripts' ) );
		add_action( 'wp_ajax_wposes-dismiss-notice', array( $this, 'ajax_dismiss_notice' ) );
	}

	/**
	 * As this class is a singleton it should not be clone-able.
	 */
	protected function __clone() {
		// Singleton.
	}

	/**
	 * Create a notice.
	 *
	 * @param string $message The message for the notice.
	 * @param array  $args    Additional parameters for the notice.
	 *
	 * @return string notice ID
	 */
	public function add_notice( $message, $args = array() ) {
		$defaults = array(
			'type'                  => 'info',
			'dismissible'           => true,
			'inline'                => false,
			'flash'                 => true,
			'only_show_to_user'     => true, // The user who has initiated an action resulting in notice. Otherwise show to all users.
			'user_capabilities'     => array( 'wposes_compat_check', 'check_capabilities' ), // A user with these capabilities can see the notice. Can be a callback with the first array item the name of global class instance.
			'only_show_in_settings' => false,
			'only_show_on_tab'      => false, // Only show on a specific WP Offload SES tab.
			'custom_id'             => '',
			'auto_p'                => true, // Automatically wrap the message in <p> tags.
			'class'                 => '', // Extra classes for the notice.
			'show_callback'         => false, // Callback to display extra info on notices. Passing a callback automatically handles show/hide toggle.
			'callback_args'         => array(), // Arguments to pass to the callback.
			'pre_render_callback'   => false, // Callback to call before notice render.
			'remove_on_dismiss'     => false,
			'subsite'               => false, // If the notice should only be displayed on current subsite
		);

		$notice                 = array_intersect_key( array_merge( $defaults, $args ), $defaults );
		$notice['message']      = $message;
		$notice['triggered_by'] = get_current_user_id();
		$notice['created_at']   = time();

		if ( $notice['custom_id'] ) {
			$notice['id'] = $notice['custom_id'];
		} else {
			$notice['id'] = apply_filters( 'wposes_notice_id_prefix', 'wposes-notice-' ) . sha1( trim( $notice['message'] ) );
		}

		if ( isset( $notice['only_show_on_tab'] ) && false !== $notice['only_show_on_tab'] ) {
			$notice['inline'] = true;
		}

		if ( $notice['subsite'] ) {
			$notice['subsite_id'] = get_current_blog_id();
		}

		$this->save_notice( $notice );

		return $notice['id'];
	}

	/**
	 * Save a notice.
	 *
	 * @param array $notice The notice to save.
	 */
	protected function save_notice( $notice ) {
		$user_id = get_current_user_id();

		if ( $notice['only_show_to_user'] ) {
			$notices = get_user_meta( $user_id, 'wposes_notices', true );
		} else {
			$notices = get_site_transient( 'wposes_notices' );
		}

		if ( ! is_array( $notices ) ) {
			$notices = array();
		}

		if ( ! array_key_exists( $notice['id'], $notices ) ) {
			$notices[ $notice['id'] ] = $notice;

			if ( $notice['only_show_to_user'] ) {
				update_user_meta( $user_id, 'wposes_notices', $notices );
			} else {
				set_site_transient( 'wposes_notices', $notices );
			}
		}
	}

	/**
	 * Remove a notice
	 *
	 * @param array $notice The notice to remove.
	 */
	public function remove_notice( $notice ) {
		$user_id = get_current_user_id();

		if ( $notice['only_show_to_user'] ) {
			$notices = get_user_meta( $user_id, 'wposes_notices', true );
		} else {
			$notices = get_site_transient( 'wposes_notices' );
		}

		if ( ! is_array( $notices ) ) {
			$notices = array();
		}

		if ( array_key_exists( $notice['id'], $notices ) ) {
			unset( $notices[ $notice['id'] ] );

			if ( $notice['only_show_to_user'] ) {
				$this->update_user_meta( $user_id, 'wposes_notices', $notices );
			} else {
				$this->set_site_transient( 'wposes_notices', $notices );
			}
		}
	}

	/**
	 * Remove a notice by it's ID.
	 *
	 * @param string $notice_id The ID of the notice to remove.
	 */
	public function remove_notice_by_id( $notice_id ) {
		$notice = $this->find_notice_by_id( $notice_id );
		if ( $notice ) {
			$this->remove_notice( $notice );
		}
	}

	/**
	 * Dismiss a notice.
	 *
	 * @param string $notice_id The ID of the notice to dismiss.
	 */
	protected function dismiss_notice( $notice_id ) {
		$user_id = get_current_user_id();

		$notice = $this->find_notice_by_id( $notice_id );
		if ( $notice ) {
			if ( $notice['remove_on_dismiss'] ) {
				$this->remove_notice_by_id( $notice_id );
			} elseif ( $notice['only_show_to_user'] ) {
				$notices = get_user_meta( $user_id, 'wposes_notices', true );
				unset( $notices[ $notice['id'] ] );

				$this->update_user_meta( $user_id, 'wposes_notices', $notices );
			} else {
				$dismissed_notices = $this->get_dismissed_notices( $user_id );

				if ( ! in_array( $notice['id'], $dismissed_notices ) ) {
					$dismissed_notices[] = $notice['id'];
					update_user_meta( $user_id, 'wposes_dismissed_notices', $dismissed_notices );
				}
			}
		}
	}

	/**
	 * Check if a notice has been dismissed for the current user.
	 *
	 * @param null|int $user_id The ID of the user to check.
	 *
	 * @return array
	 */
	public function get_dismissed_notices( $user_id = null ) {
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$dismissed_notices = get_user_meta( $user_id, 'wposes_dismissed_notices', true );
		if ( ! is_array( $dismissed_notices ) ) {
			$dismissed_notices = array();
		}

		return $dismissed_notices;
	}

	/**
	 * Un-dismiss a notice for a user.
	 *
	 * @param string     $notice_id         The ID of the notice to be un-dismissed.
	 * @param null|int   $user_id           The ID of the user to un-dismiss the notice for.
	 * @param null|array $dismissed_notices An array of dismissed notices.
	 */
	public function undismiss_notice_for_user( $notice_id, $user_id = null, $dismissed_notices = null ) {
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( is_null( $dismissed_notices ) ) {
			$dismissed_notices = $this->get_dismissed_notices( $user_id );
		}

		$key = array_search( $notice_id, $dismissed_notices );
		unset( $dismissed_notices[ $key ] );

		$this->update_user_meta( $user_id, 'wposes_dismissed_notices', $dismissed_notices );
	}

	/**
	 * Un-dismiss a notice for all users that have dismissed it.
	 *
	 * @param string $notice_id The ID of the notice to be un-dimissed.
	 */
	public function undismiss_notice_for_all( $notice_id ) {
		$args = array(
			'meta_key'     => 'wposes_dismissed_notices',
			'meta_value'   => $notice_id,
			'meta_compare' => 'LIKE',
		);

		$users = get_users( $args );

		foreach ( $users as $user ) {
			$this->undismiss_notice_for_user( $notice_id, $user->ID );
		}
	}

	/**
	 * Find a notice by it's ID.
	 *
	 * @param string $notice_id The ID of the notice to find.
	 *
	 * @return array|null
	 */
	public function find_notice_by_id( $notice_id ) {
		$user_id = get_current_user_id();

		$user_notices = get_user_meta( $user_id, 'wposes_notices', true );
		if ( ! is_array( $user_notices ) ) {
			$user_notices = array();
		}

		$global_notices = get_site_transient( 'wposes_notices' );
		if ( ! is_array( $global_notices ) ) {
			$global_notices = array();
		}
		$notices = array_merge( $user_notices, $global_notices );

		if ( array_key_exists( $notice_id, $notices ) ) {
			return $notices[ $notice_id ];
		}

		return null;
	}

	/**
	 * Show the notices
	 *
	 * @param string $tab The tab to maybe show the notice on.
	 */
	public function admin_notices( $tab = '' ) {
		if ( empty( $tab ) ) {
			// Callbacks with no $tab property return empty string, so convert to bool.
			$tab = false;
		}

		$user_id           = get_current_user_id();
		$dismissed_notices = get_user_meta( $user_id, 'wposes_dismissed_notices', true );
		if ( ! is_array( $dismissed_notices ) ) {
			$dismissed_notices = array();
		}

		$user_notices = get_user_meta( $user_id, 'wposes_notices', true );
		$user_notices = $this->cleanup_corrupt_user_notices( $user_id, $user_notices );
		if ( is_array( $user_notices ) && ! empty( $user_notices ) ) {
			foreach ( $user_notices as $notice ) {
				$this->maybe_show_notice( $notice, $dismissed_notices, $tab );
			}
		}

		$global_notices = get_site_transient( 'wposes_notices' );
		if ( is_array( $global_notices ) && ! empty( $global_notices ) ) {
			foreach ( $global_notices as $notice ) {
				$this->maybe_show_notice( $notice, $dismissed_notices, $tab );
			}
		}
	}

	/**
	 * Cleanup corrupt user notices. Corrupt notices start with a
	 * numerically indexed array, opposed to string ID
	 *
	 * @param int   $user_id The ID of the user to clean up corrupt notices for.
	 * @param array $notices An array of notices.
	 *
	 * @return array
	 */
	protected function cleanup_corrupt_user_notices( $user_id, $notices ) {
		if ( ! is_array( $notices ) || empty( $notices ) ) {
			return $notices;
		}

		foreach ( $notices as $key => $notice ) {
			if ( is_int( $key ) ) {
				// Corrupt, remove notice.
				unset( $notices[ $key ] );

				$this->update_user_meta( $user_id, 'wposes_notices', $notices );
			}
		}

		return $notices;
	}

	/**
	 * If it should be shown, display an individual notice
	 *
	 * @param array       $notice            The notice to be shown.
	 * @param array       $dismissed_notices An array of dismissed notices.
	 * @param string|bool $tab               The current tab.
	 */
	protected function maybe_show_notice( $notice, $dismissed_notices, $tab ) {
		$screen = get_current_screen();
		if ( $notice['only_show_in_settings'] && false === strpos( $screen->id, $this->wp_offload_ses->hook_suffix ) ) {
			return;
		}

		if ( ! $notice['only_show_to_user'] && in_array( $notice['id'], $dismissed_notices ) ) {
			return;
		}

		if ( ! isset( $notice['only_show_on_tab'] ) && false !== $tab ) {
			return;
		}

		if ( isset( $notice['only_show_on_tab'] ) && $tab !== $notice['only_show_on_tab'] ) {
			return;
		}

		if ( ! $this->check_capability_for_notice( $notice ) ) {
			return;
		}

		if ( $notice['subsite'] && $notice['subsite_id'] !== get_current_blog_id() ) {
			return;
		}

		if ( 'info' === $notice['type'] ) {
			$notice['type'] = 'notice-info';
		}

		if ( ! empty( $notice['pre_render_callback'] ) && is_callable( $notice['pre_render_callback'] ) ) {
			call_user_func( $notice['pre_render_callback'] );
		}

		$this->wp_offload_ses->render_view( 'notice', $notice );

		if ( $notice['flash'] ) {
			$this->remove_notice( $notice );
		}
	}

	/**
	 * Ensure the user has the correct capabilities for the notice to be displayed.
	 *
	 * @param array $notice The notice to check the capability for.
	 *
	 * @return bool|mixed
	 */
	protected function check_capability_for_notice( $notice ) {
		if ( ! isset( $notice['user_capabilities'] ) || empty( $notice['user_capabilities'] ) ) {
			// No capability restrictions, show the notice.
			return true;
		}

		$caps = $notice['user_capabilities'];

		if ( 2 === count( $caps ) && isset( $GLOBALS[ $caps[0] ] ) && is_callable( array( $GLOBALS[ $caps[0] ], $caps[1] ) ) ) {
			// Handle callback passed for capabilities.
			return call_user_func( array( $GLOBALS[ $caps[0] ], $caps[1] ) );
		}

		foreach ( $caps as $cap ) {
			if ( is_string( $cap ) && ! current_user_can( $cap ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Enqueue notice scripts in the admin
	 */
	public function enqueue_notice_scripts() {
		$this->wp_offload_ses->enqueue_style( 'wposes-notice', 'assets/css/notice' );
		$this->wp_offload_ses->enqueue_script( 'wposes-notice', 'assets/js/notice', array( 'jquery' ) );

		wp_localize_script(
			'wposes-notice',
			'wposes_notice',
			array(
				'strings' => array(
					'dismiss_notice_error' => __( 'Error dismissing notice.', 'wp-offload-ses' ),
				),
				'nonces'  => array(
					'dismiss_notice' => wp_create_nonce( 'wposes-dismiss-notice' ),
				),
			)
		);
	}

	/**
	 * Handler for AJAX request to dismiss a notice
	 */
	public function ajax_dismiss_notice() {
		$this->wp_offload_ses->verify_ajax_request();

		if ( ! isset( $_POST['notice_id'] ) || ! ( $notice_id = sanitize_text_field( $_POST['notice_id'] ) ) ) { // phpcs:ignore
			$out = array( 'error' => __( 'Invalid notice ID.', 'wp-offload-ses' ) );
			$this->wp_offload_ses->end_ajax( $out );
		}

		$this->dismiss_notice( $notice_id );

		$out = array(
			'success' => '1',
		);
		$this->wp_offload_ses->end_ajax( $out );
	}

	/**
	 * Helper to update/delete user meta
	 *
	 * @param int    $user_id The ID of the user to update/delete meta for.
	 * @param string $key     The key of the meta to update/delete.
	 * @param array  $value   The value to update.
	 */
	protected function update_user_meta( $user_id, $key, $value ) {
		if ( empty( $value ) ) {
			delete_user_meta( $user_id, $key );
		} else {
			update_user_meta( $user_id, $key, $value );
		}
	}

	/**
	 * Helper to update/delete site transient
	 *
	 * @param string $key   The key of the site transient to update/delete.
	 * @param array  $value The value of the site transient to update.
	 */
	protected function set_site_transient( $key, $value ) {
		if ( empty( $value ) ) {
			delete_site_transient( $key );
		} else {
			set_site_transient( $key, $value );
		}
	}

}
