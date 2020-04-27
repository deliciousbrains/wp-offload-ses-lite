<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\WP_Queue\Queue;
use DeliciousBrains\WP_Offload_SES\Queue\Jobs\Email_Job;
use DeliciousBrains\WP_Offload_SES\Queue\Connection;
use DeliciousBrains\WP_Offload_SES\Queue\Email_Cron;

/**
 * Class Email_Queue
 *
 * @since 1.0.0
 */
class Email_Queue extends Queue {

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	protected $connection;

	/**
	 * The cron class.
	 *
	 * @var Email_Cron
	 */
	protected $cron;

	/**
	 * The delay before running a cron.
	 *
	 * @var int
	 */
	private $delay = 0;

	/**
	 * The number of times to attempt a job.
	 *
	 * @var int
	 */
	private $cron_attempts = 3;

	/**
	 * The minimum time between cron jobs.
	 *
	 * @var int
	 */
	private $cron_interval = 1;

	/**
	 * The table to store jobs.
	 *
	 * @var string
	 */
	private $jobs_table = 'oses_jobs';

	/**
	 * The table to store failures.
	 *
	 * @var string
	 */
	private $failures_table = 'oses_failures';

	/**
	 * Queue constructor
	 */
	public function __construct() {
		global $wpdb;

		$this->connection = new Connection( $wpdb );
		parent::__construct( $this->connection );

		$this->add_cron();

		add_action( 'wp_ajax_wposes_trigger_queue', array( $this, 'maybe_process_queue' ) );
		add_action( 'wp_ajax_nopriv_wposes_trigger_queue', array( $this, 'maybe_process_queue' ) );
	}

	/**
	 * Create a cron worker.
	 *
	 * @param int $attempts
	 * @param int $interval
	 *
	 * @return Cron
	 */
	public function cron( $attempts = 3, $interval = 5 ) {
		if ( is_null( $this->cron ) ) {
			$this->cron = new Email_Cron( get_class( $this->connection ), $this->worker( $attempts ), $interval );
			$this->cron->init();
		}

		return $this->cron;
	}

	/**
	 * Maybe process a bit of the queue now.
	 *
	 * @return bool.
	 */
	public function maybe_process_queue() {
		if ( ! isset( $_REQUEST['action'] ) || 'wposes_trigger_queue' !== $_REQUEST['action'] ) {
			return false;
		}

		check_ajax_referer( 'wposes_trigger_queue', 'nonce' );

		$this->cron->cron_worker();
	}

	/**
	 * Add an email to the queue.
	 *
	 * @param int $email_id   The ID of the email to process.
	 * @param int $subsite_id The ID of the subsite sending the email.
	 *
	 * @return int|bool
	 */
	public function process_email( $email_id, $subsite_id = 0 ) {
		if ( 0 === $subsite_id ) {
			$subsite_id = get_current_blog_id();
		}

		$job = new Email_Job( $email_id, $subsite_id );

		return $this->push( $job, $this->delay );
	}

	/**
	 * Remove an email from the queue.
	 *
	 * @param int $email_id The ID of the email to remove.
	 */
	public function cancel_email( $email_id ) {
		return $this->connection->cancel_email( $email_id );
	}

	/**
	 * Set up the cron for the queue
	 */
	public function add_cron() {
		$this->cron( $this->cron_attempts, $this->cron_interval );
	}

	/**
	 * Use our custom worker
	 *
	 * @param int $attempts The number of times to attempt the job.
	 *
	 * @return Worker
	 */
	public function worker( $attempts ) {
		return new Worker( $this->connection, $attempts );
	}

	/**
	 * Create the database tables if necessary
	 */
	public function install_tables() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$wpdb->hide_errors();

		$charset_collate = $wpdb->get_charset_collate();
		$default_subsite = defined( 'BLOG_ID_CURRENT_SITE' ) ? constant( 'BLOG_ID_CURRENT_SITE' ) : 1;

		$sql = "CREATE TABLE {$wpdb->base_prefix}{$this->jobs_table} (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				email_id bigint(20),
				subsite_id BIGINT(20) DEFAULT '$default_subsite',
				job longtext NOT NULL,
				attempts tinyint(3) NOT NULL DEFAULT 0,
				reserved_at datetime DEFAULT NULL,
				available_at datetime NOT NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->base_prefix}{$this->failures_table} (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				job longtext NOT NULL,
				error text DEFAULT NULL,
				failed_at datetime NOT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";
		dbDelta( $sql );
	}

}
