<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\WP_Queue\Cron;

/**
 * Class Email_Cron
 *
 * @since 1.1
 */
class Email_Cron extends Cron {

	/**
	 * Cron constructor.
	 *
	 * @param string $id
	 * @param Worker $worker
	 * @param int    $interval
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
		if ( $this->is_worker_locked() ) {
			return;
		}

		$this->start_time = time();
		$this->lock_worker();

		update_option( 'wposes_last_cron_run', $this->start_time );

		while ( ! $this->time_exceeded() && ! $this->memory_exceeded() ) {
			if ( ! $this->worker->process() ) {
				break;
			}
		}

		$this->unlock_worker();
	}

}
