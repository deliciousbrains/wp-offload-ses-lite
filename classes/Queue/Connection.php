<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Connections\DatabaseConnection;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Job;
use Exception;

/**
 * Class Connection
 *
 * @since 1.0.0
 */
class Connection extends DatabaseConnection {

	/**
	 * Construct the Connection class.
	 *
	 * @param \wpdb $wpdb WordPress database class.
	 */
	public function __construct( $wpdb, array $allowed_job_classes = array() ) {
		parent::__construct( $wpdb, $allowed_job_classes );

		$this->jobs_table     = $this->database->base_prefix . 'oses_jobs';
		$this->failures_table = $this->database->base_prefix . 'oses_failures';
	}

	/**
	 * Retrieve a job by ID
	 *
	 * @param int $id The ID of the job to retrieve.
	 *
	 * @return bool|Job
	 */
	public function get_job( $id ) {
		$sql     = $this->database->prepare( "SELECT * FROM {$this->jobs_table} WHERE id = %d", $id );
		$raw_job = $this->database->get_row( $sql );

		if ( is_null( $raw_job ) ) {
			return false;
		}

		return $this->vitalize_job( $raw_job );
	}

	/**
	 * Release a job back onto the queue.
	 *
	 * @param Job $job
	 *
	 * @return bool
	 */
	public function release( Job $job ) {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$data  = array(
			'job'         => serialize( $job ),
			'attempts'    => $job->attempts(),
			'reserved_at' => null,
		);
		$where = array( 'id' => $job->id() );

		$wp_offload_ses->get_email_log()->update_email( $job->email_id, 'auto_retries', $job->attempts() );

		if ( $this->database->update( $this->jobs_table, $data, $where ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Push a job onto the failure queue and mark the email as failed in the email log.
	 *
	 * @param Job       $job       The job that failed.
	 * @param Exception $exception The exception that caused the failure.
	 *
	 * @return bool
	 */
	public function failure( $job, Exception $exception ): bool {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$wp_offload_ses->add_failed_email_notice();
		$wp_offload_ses->get_email_log()->update_email( $job->email_id, 'email_status', 'failed' );

		return parent::failure( $job, $exception );
	}

	/**
	 * Push a job onto the queue.
	 *
	 * @param Job $job   The email job.
	 * @param int $delay The delay for the job.
	 *
	 * @return bool|int
	 */
	public function push( Job $job, $delay = 0 ) {
		$args = array(
			'job'          => serialize( $job ),
			'available_at' => $this->datetime( $delay ),
			'created_at'   => $this->datetime(),
			'email_id'     => $job->email_id,
			'subsite_id'   => $job->subsite_id,
		);

		$result = $this->database->insert( $this->jobs_table, $args );

		if ( ! $result ) {
			return false;
		}

		return $this->database->insert_id;
	}

	/**
	 * Retrieve a job from the queue.
	 *
	 * @return bool|Job
	 */
	public function pop() {
		global $wp_offload_ses;

		$this->release_reserved();

		$subsite_sql = '';

		if ( is_multisite() ) {
			$subsite_settings_enabled = (bool) $wp_offload_ses->settings->get_setting( 'enable-subsite-settings', false );

			if ( $subsite_settings_enabled ) {
				$subsite_id  = get_current_blog_id();
				$subsite_sql = "AND subsite_id = $subsite_id";
			}
		}

		$sql     = $this->database->prepare( "SELECT * FROM {$this->jobs_table} WHERE reserved_at IS NULL AND available_at <= %s $subsite_sql ORDER BY available_at LIMIT 1", $this->datetime() );
		$raw_job = $this->database->get_row( $sql );

		if ( is_null( $raw_job ) ) {
			return false;
		}

		$job = $this->vitalize_job( $raw_job );
		$this->reserve( $job );

		return $job;
	}

	/**
	 * Cancel an email from being processed by the queue.
	 *
	 * @param int $email_id The ID of the email to cancel.
	 *
	 * @return bool
	 */
	public function cancel_email( $email_id ) {
		$where = array( 'email_id' => (int) $email_id );

		if ( $this->database->delete( $this->jobs_table, $where ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get total jobs in the queue.
	 *
	 * @param bool $unreserved If we should just get the # of unreserved jobs.
	 *
	 * @return int
	 */
	public function jobs( $unreserved = false ) {
		global $wp_offload_ses;

		$sql = "SELECT COUNT(*) FROM {$this->jobs_table} WHERE 1=1";

		if ( $unreserved ) {
			$sql .= ' AND reserved_at IS NULL';
		}

		if ( is_multisite() ) {
			$subsite_settings_enabled = (bool) $wp_offload_ses->settings->get_setting( 'enable-subsite-settings', false );

			if ( $subsite_settings_enabled ) {
				$sql .= $this->database->prepare( ' AND subsite_id = %d', get_current_blog_id() );
			}
		}

		return (int) $this->database->get_var( $sql );
	}

}
