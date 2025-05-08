<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Cron;

/**
 * Class Email_Cron
 *
 * @since 1.1
 */
class Email_Cron extends Cron {
	/**
	 * @var Email_Worker
	 */
	protected $worker;

	/**
	 * Cron constructor.
	 *
	 * @param string       $id
	 * @param Email_Worker $worker
	 * @param int          $interval
	 */
	public function __construct( $id, $worker, $interval ) {
		parent::__construct( $id, $worker, $interval );
	}

	/**
	 * Init cron class.
	 *
	 * @return bool
	 */
	public function init() {
		if ( ! $this->is_enabled() ) {
			return false;
		}

		add_filter( 'cron_schedules', array( $this, 'schedule_cron' ) );
		add_action( $this->id, array( $this, 'cron_worker' ) );

		if ( ! wp_next_scheduled( $this->id ) ) {
			// Schedule cron in one min. to allow upgrade routines to run.
			wp_schedule_event( strtotime( '+60 seconds' ), $this->id, $this->id );
		}

		return true;
	}

	/**
	 * Process any jobs in the queue.
	 */
	public function cron_worker() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		// We have to late-check whether only enqueueing here as settings are not
		// realized until after init.
		if ( $this->is_worker_locked() || $wp_offload_ses->settings->get_setting( 'enqueue-only', false ) ) {
			return;
		}

		$this->start_time = time();
		$this->lock_worker();

		update_option( 'wposes_last_cron_run', $this->start_time );

		while ( ! $this->time_exceeded() && ! $this->memory_exceeded() ) {
			// Puts the next job into the command pool.
			// If the command pool is full (we've hit the per second rate limit),
			// or queue emptied, the command pool will be executed and cleared.
			if ( ! $this->worker->process() ) {
				break;
			}
		}

		// Executes remaining commands in the command pool.
		$this->worker->cleanup();

		$this->unlock_worker();
	}

	/**
	 * Get PHP memory limit.
	 *
	 * @return int
	 */
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			$memory_limit = '256M';
		}
		if ( ! $memory_limit || -1 == $memory_limit ) {
			// Unlimited, set to 1GB
			$memory_limit = '1000M';
		}

		return wp_convert_hr_to_bytes( $memory_limit );
	}
}
