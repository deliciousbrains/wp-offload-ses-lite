<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\Email;
use DeliciousBrains\WP_Offload_SES\Notices;
use DeliciousBrains\WP_Offload_SES\Utils;
use DeliciousBrains\WP_Offload_SES\Error;
use DeliciousBrains\WP_Offload_SES\Queue\Connection;
use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;

class Queue_Status {

	/**
	 * The Connection class.
	 *
	 * @var Connection
	 */
	private $connection;

	/**
	 * The WP_Offload_SES class.
	 *
	 * @var WP_Offload_SES
	 */
	private $wp_offload_ses;

	/**
	 * Constructs the Queue_Status class.
	 *
	 * @param WP_Offload_SES $wp_offload_ses
	 */
	public function __construct( $wp_offload_ses ) {
		global $wpdb;

		$this->connection     = new Connection( $wpdb );
		$this->wp_offload_ses = $wp_offload_ses;

		// Run the cron health check after everything has loaded.
		add_action( 'wp_loaded', array( $this, 'check_status' ) );
	}

	/**
	 * Gets the time of the last cron check,
	 * or false if it hasn't happened.
	 *
	 * @return int
	 */
	public function get_last_cron_check() {
		return (int) get_option( 'wposes_last_cron_check', 0 );
	}

	/**
	 * Gets the time of the last cron run,
	 * or false if it hasn't happened.
	 *
	 * @return int
	 */
	public function get_last_cron_run() {
		return (int) get_option( 'wposes_last_cron_run', 0 );
	}

	/**
	 * Gets the time of the next scheduled event,
	 * or false if it hasn't been scheduled.
	 *
	 * @return string|bool
	 */
	public function get_next_scheduled() {
		return wp_next_scheduled( 'deliciousbrains_wp_offload_ses_queue_connection' );
	}

	/**
	 * Return the number of jobs.
	 *
	 * @return int
	 */
	public function get_total_jobs() {
		return $this->connection->jobs();
	}

	/**
	 * Return the number of failures.
	 *
	 * @return int
	 */
	public function get_total_failures() {
		return $this->connection->failed_jobs();
	}

	/**
	 * Checks if the default WP Cron is being used.
	 *
	 * @return bool
	 */
	public function is_wp_cron_enabled() {
		return ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) ? false : true;
	}

	/**
	 * Checks if the alternate WP Cron is being used.
	 *
	 * @return bool
	 */
	public function is_alternate_wp_cron() {
		return ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) ? true : false;
	}

	/**
	 * Should the status check even be running?
	 *
	 * @return bool
	 */
	public function should_check_status() {
		// If the site isn't configured to send email, definitely not.
		if ( ! $this->wp_offload_ses->settings->get_setting( 'send-via-ses', false ) ) {
			return false;
		}

		if ( is_multisite() ) {
			$subsite_settings_enabled = (bool) $this->wp_offload_ses->settings->get_setting( 'enable-subsite-settings', false );

			// If subsite settings are disabled, all emails are sent on the back of the main site cron.
			if ( ! $subsite_settings_enabled && ! is_main_site() ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Makes sure the cron is running, raises an alert
	 * if not.
	 */
	public function check_status() {
		if ( get_transient( 'wposes_doing_cron_check' ) || ! $this->should_check_status() ) {
			return false;
		}

		set_transient( 'wposes_doing_cron_check', true );

		$last_check = $this->get_last_cron_check();
		$time_since = 0;

		if ( $last_check ) {
			$time_since = time() - $last_check;
		}

		/**
		 * The minimum time in between cron checks.
		 *
		 * @param int $time The time in seconds, six hours by default.
		 *
		 * @return int $time The time in seconds.
		 */
		$check_interval = apply_filters( 'wposes_cron_check_interval', 6 * HOUR_IN_SECONDS );

		// If there hasn't been a check, or if it's been over six hours since the last check...
		if ( ! $last_check || $time_since >= (int) $check_interval ) {
			$last_run = $this->get_last_cron_run();

			/**
			 * There was a check before, and there's still no cron run,
			 * or the last run was before the last check (if there was a check).
			 */
			if ( ( $last_check && ! $last_run ) || $last_run < $last_check ) {
				$this->add_cron_error_notice();
				$this->maybe_send_cron_error_email();
			} elseif ( $last_run ) {
				// It is running normally as far as we can tell.
				delete_option( 'wposes_cron_error_email' );
				$this->wp_offload_ses->get_notices()->remove_notice_by_id( 'wposes_cron_error' );
			}

			// Update the last check so we know we don't need to check again.
			update_option( 'wposes_last_cron_check', time() );
		}

		delete_transient( 'wposes_doing_cron_check' );
	}

	/**
	 * Notify admins about a cron issue.
	 */
	public function add_cron_error_notice() {
		$message  = __( '<strong>WP Offload SES</strong> &mdash; WP Cron does not appear to be working correctly.', 'wp-offload-ses' );
		$count    = $this->get_total_jobs();
		
		if ( 0 !== $count ) {
			$message .= ' ';
			$message .=sprintf(
				_n( 'There is currently %d email in the queue that may not be sent until this is resolved.', 'There are currently %d emails in the queue that may not be sent until this is resolved.', $count, 'wp-offload-ses' ),
				$count
			);
		}

		$message .= ' ';
		$message .= sprintf(
			__( 'Please see our <a href="%s">cron setup doc</a> for more details.', 'wp-offload-ses' ),
			$this->wp_offload_ses->dbrains_url(
				'/wp-offload-ses/doc/cron-setup/',
				array(
					'utm_campaign' => 'error+messages',
				)
			)
		);

		$this->wp_offload_ses->get_notices()->add_notice(
			$message,
			array(
				'type'                  => 'error',
				'only_show_to_user'     => false,
				'flash'                 => false,
				'remove_on_dismiss'     => true,
				'custom_id'             => 'wposes_cron_error',
				'subsite'               => true,
			)
		);
	}

	/**
	 * Send an email to admin about cron issue if we haven't already.
	 *
	 * @return bool
	 */
	public function maybe_send_cron_error_email() {
		/**
		 * Filter to disable the cron error email. Useful on development sites
		 * where a dedicated server cron isn't necessary.
		 *
		 * @param bool $send_email If the email should be sent.
		 *
		 * @return bool
		 */
		$send_email = apply_filters( 'wposes_send_cron_error_email', true );

		if ( ! $send_email || get_option( 'wposes_cron_error_email' ) ) {
			return false;
		}

		$url     = Utils::reduce_url( get_option( 'siteurl' ) );
		$url     = str_replace( '//', '', $url );
		$to      = get_site_option( 'admin_email' );
		$subject = __( 'WP Offload SES - Cron not running', 'wp-offload-ses' );
		$message = __( 'Hi,', 'wp-offload-ses' );
		$message .= '<br><br>';
		$message .= sprintf(
			__( 'You\'re receiving this email because WP Cron doesn\'t appear to be running on %s.', 'wp-offload-ses' ),
			$url,
			$this->get_total_jobs()
		);
	
		$count = $this->get_total_jobs();

		if ( 0 !== $count ) {
			$message .= ' ';
			$message .=sprintf(
				_n( 'There is currently %d email in the queue that may not be sent until this is resolved.', 'There are currently %d emails in the queue that may not be sent until this is resolved.', $count, 'wp-offload-ses' ),
				$count
			);
		}

		$message .= '<br><br>';
		$message .= sprintf(
			__( 'Please see our <a href="%s">cron setup doc</a> for more details.', 'wp-offload-ses' ),
			$this->wp_offload_ses->dbrains_url(
				'/wp-offload-ses/doc/cron-setup/',
				array(
					'utm_campaign' => 'error+messages',
				)
			)
		);

		// Send the email.
		try {
			$email  = new Email( $to, $subject, $message, 'Content-Type: text/html', '' );
			$raw    = $email->prepare();
			$result = $this->wp_offload_ses->get_ses_api()->send_email( $raw );
		} catch ( \Exception $e ) {
			// Log the error and move on...
			new Error( Error::$send_cron_email, __( 'There was an error trying to send the cron alert.', 'wp-offload-ses' ) );
		}

		// Make sure we don't keep sending these out.
		update_option( 'wposes_cron_error_email', true );

		if ( ! $result ) {
			return false;
		}

		return true;
	}

}
