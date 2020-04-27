<?php
/**
 * Log emails for WP Offload SES.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;

/**
 * Class Email_Log
 *
 * @since 1.0.0
 */
class Email_Log {

	/**
	 * The WordPress database class.
	 *
	 * @var \WPDB
	 */
	private $database;

	/**
	 * The table to log emails to.
	 *
	 * @var string
	 */
	private $log_table;

	/**
	 * The table to log email clicks.
	 *
	 * @var string
	 */
	private $clicks_table;

	/**
	 * The table to log attachments.
	 *
	 * @var string
	 */
	private $attachments_table;

	/**
	 * The table to log email attachments.
	 *
	 * @var string
	 */
	private $email_attachments_table;

	/**
	 * Construct the log class.
	 */
	public function __construct() {
		global $wpdb;

		$this->database = $wpdb;

		$this->log_table               = $this->database->base_prefix . 'oses_emails';
		$this->clicks_table            = $this->database->base_prefix . 'oses_clicks';
		$this->attachments_table       = $this->database->base_prefix . 'oses_attachments';
		$this->email_attachments_table = $this->database->base_prefix . 'oses_email_attachments';

		$this->schedule_cron();
		add_action( 'deliciousbrains_wp_offload_ses_log_cleanup', array( $this, 'log_cleanup' ) );
	}

	/**
	 * Add an email to the log.
	 *
	 * @param array $atts The attributes of the email to log.
	 *
	 * @return int|bool
	 */
	public function log_email( $atts ) {
		$atts = array_map( 'maybe_serialize', $atts );
		$args = array(
			'email_to'          => isset( $atts['to'] ) ? $atts['to'] : '',
			'email_subject'     => isset( $atts['subject'] ) ? $atts['subject'] : '',
			'email_message'     => isset( $atts['message'] ) ? $atts['message'] : '',
			'email_headers'     => isset( $atts['headers'] ) ? $atts['headers'] : '',
			'email_attachments' => isset( $atts['attachments'] ) ? $atts['attachments'] : '',
			'email_status'      => 'queued',
			'email_created'     => current_time( 'mysql' ),
		);

		if ( is_multisite() ) {
			$args['subsite_id'] = isset( $atts['subsite_id'] ) ? $atts['subsite_id'] : get_current_blog_id();
		}

		if ( isset( $atts['parent'] ) ) {
			$args['email_parent'] = $atts['parent'];
		}

		$result = $this->database->insert(
			$this->log_table,
			$args
		);

		if ( ! $result ) {
			return false;
		}

		return $this->database->insert_id;
	}

	/**
	 * Get an email from the log.
	 *
	 * @param int $id The ID of the email to get.
	 *
	 * @return array|bool
	 */
	public function get_email( $id ) {
		$sql = $this->database->prepare( "SELECT * FROM {$this->log_table} WHERE email_id = %d", $id );
		$row = $this->database->get_row( $sql, ARRAY_A );

		if ( is_null( $row ) ) {
			return false;
		}

		$row = array_map( 'maybe_unserialize', $row );

		return $row;
	}

	/**
	 * Update an email in the database.
	 *
	 * @param int    $email_id The ID of the email to update.
	 * @param string $key      The field to update.
	 * @param mixed  $value    The value of the update.
	 *
	 * @return bool
	 */
	public function update_email( $email_id, $key, $value ) {
		$result = $this->database->update(
			$this->log_table,
			array( $key => maybe_serialize( $value ) ),
			array( 'email_id' => $email_id ),
			array( '%s' ),
			array( '%d' )
		);

		if ( ! $result ) {
			return false;
		}

		return true;
	}

	/**
	 * Delete an email from the database.
	 *
	 * @param int $email_id The ID of the email to delete.
	 *
	 * @return bool
	 */
	public function delete_email( $email_id ) {
		$result = $this->database->delete(
			$this->log_table,
			array( 'email_id' => $email_id ),
			array( '%d' )
		);

		if ( ! $result ) {
			return false;
		}

		return true;
	}

	/**
	 * Schedule the cron to clean up the log tables.
	 */
	private function schedule_cron() {
		if ( ! wp_next_scheduled( 'deliciousbrains_wp_offload_ses_log_cleanup' ) ) {
			wp_schedule_event( strtotime( '+1 day' ), 'daily', 'deliciousbrains_wp_offload_ses_log_cleanup' );
		}
	}

	/**
	 * Return the currently selected log duration.
	 *
	 * @return int
	 */
	public function get_log_duration() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$duration = (int) $wp_offload_ses->settings->get_setting( 'log-duration' );

		if ( ! array_key_exists( $duration, self::get_log_durations() ) ) {
			$duration = 90;
		}

		return self::validate_duration( $duration );
	}

	/**
	 * Validates the provided log duration.
	 *
	 * @param int $duration The duration to validate.
	 *
	 * @return int
	 */
	private static function validate_duration( $duration ) {
		$options = array(
			'options' => array(
				'default'   => 90,
				'min_range' => 0,
			),
		);

		return filter_var( $duration, FILTER_VALIDATE_INT, $options );
	}

	/**
	 * Return the available log durations.
	 *
	 * @return array
	 */
	public static function get_log_durations() {
		$default_durations = array(
			'7'   => __( 'After 7 days', 'wp-offload-ses' ),
			'30'  => __( 'After 30 days', 'wp-offload-ses' ),
			'60'  => __( 'After 60 days', 'wp-offload-ses' ),
			'90'  => __( 'After 90 days', 'wp-offload-ses' ),
			'180' => __( 'After 6 months', 'wp-offload-ses' ),
			'365' => __( 'After 1 year', 'wp-offload-ses' ),
			'730' => __( 'After 2 years', 'wp-offload-ses' ),
		);

		$log_durations = apply_filters( 'wposes_log_durations', $default_durations );

		if ( ! is_array( $log_durations ) ) {
			return $default_durations;
		}

		foreach ( $log_durations as $duration => $text ) {
			if ( self::validate_duration( $duration ) !== (int) $duration ) {
				unset( $log_durations[ $duration ] );
				continue;
			}

			if ( ! is_string( $text ) || empty( $text ) ) {
				unset( $log_durations[ $duration ] );
			}
		}

		ksort( $log_durations );

		return $log_durations;
	}

	/**
	 * Remove outdated logs.
	 */
	public function log_cleanup() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		if ( ! wp_doing_cron() ) {
			return;
		}

		$duration = $this->get_log_duration();

		if ( 0 === $duration ) {
			return;
		}

		$subsite_sql = '';

		if ( is_multisite() ) {
			$subsite_id  = get_current_blog_id();
			$subsite_sql = "AND $this->log_table.subsite_id = $subsite_id";
		}

		// Delete emails and clicks for emails outside of the retention period.
		$query = "DELETE $this->log_table, $this->clicks_table FROM $this->log_table
					LEFT JOIN $this->clicks_table ON $this->log_table.email_id = $this->clicks_table.email_id
					WHERE $this->log_table.email_created < NOW() - INTERVAL $duration DAY
					$subsite_sql";
		$this->database->query( $query );

		// Delete any records from link table that no longer exist.
		$query = "DELETE {$this->email_attachments_table}
					FROM {$this->email_attachments_table}
					LEFT JOIN {$this->log_table} ON {$this->email_attachments_table}.email_id = {$this->log_table}.email_id
					WHERE {$this->log_table}.email_id IS NULL";
		$this->database->query( $query );

		// Flag any attachments that can be safely deleted.
		$query = "UPDATE {$this->attachments_table} attachments
					LEFT JOIN {$this->email_attachments_table} email_attachments ON email_attachments.attachment_id = attachments.id
					SET attachments.gc = 1
					WHERE email_attachments.attachment_id IS NULL";
		$this->database->query( $query );

		// Delete the files themselves.
		$wp_offload_ses->get_attachments()->delete_attachments();
	}

	/**
	 * Install/update the log table(s) if necessary.
	 */
	public function install_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$wpdb->hide_errors();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$this->log_table} (
				`email_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
				`subsite_id` BIGINT(20),
				`email_to` TEXT NOT NULL,
				`email_subject` VARCHAR(255) NOT NULL,
				`email_message` LONGTEXT NOT NULL,
				`email_headers` LONGTEXT NOT NULL,
				`email_attachments` LONGTEXT NOT NULL,
				`email_status`  VARCHAR(20) NOT NULL,
				`email_open_count` INT DEFAULT '0',
				`email_first_open_date` DATETIME,
				`email_last_open_date` DATETIME,
				`email_created` DATETIME NOT NULL,
				`email_sent` DATETIME,
				`email_parent` BIGINT(20),
				`auto_retries` INT DEFAULT '0',
				`manual_retries` INT DEFAULT '0',
				PRIMARY KEY  (email_id),
				INDEX email_subject_v2 (email_subject(190))
				) $charset_collate;";
		dbDelta( $sql );

		$indexes = (array) $wpdb->get_results( "SHOW INDEX FROM $this->log_table", ARRAY_A );
		$indexes = wp_list_pluck( $indexes, 'Key_name' );

		if ( in_array( 'email_subject', $indexes ) ) {
			// Drop the old index.
			$wpdb->query( "ALTER TABLE $this->log_table DROP INDEX email_subject" );
		}
	}

}
