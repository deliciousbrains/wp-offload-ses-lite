<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\Command_Pool;
use DeliciousBrains\WP_Offload_SES\Error;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Exceptions\WorkerAttemptsExceededException;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Worker;
use Exception;

/**
 * Class Worker
 *
 * @since 1.0.0
 */
class Email_Worker extends Worker {

	/**
	 * The AWS Command Pool wrapper.
	 *
	 * @var Command_Pool
	 */
	protected $command_pool;

	/**
	 * Worker constructor.
	 *
	 * @param Connection $connection The database connection.
	 * @param int        $attempts   The number of times to attempt a job.
	 */
	public function __construct( Connection $connection, int $attempts = 3 ) {
		parent::__construct( $connection, $attempts );
		$this->command_pool = new Command_Pool( $connection, $attempts );
	}

	/**
	 * Process a job on the queue, adding it to the command pool.
	 *
	 * The command pool will be executed if there are no more jobs, or
	 * if the batch send limit (concurrency) is reached.
	 *
	 * Returns false if:
	 *  - no job was retrieved but there are still jobs to process; or
	 *  - the AWS command for the job could not be constructed.
	 *
	 * Returns true otherwise, but note that this does not mean the job
	 * was added to the command pool; it may have been failed or released.
	 *
	 * @return bool
	 */
	public function process(): bool {
		$job       = $this->connection->pop();
		$exception = null;

		if ( ! $job ) {
			if ( 0 !== $this->connection->jobs( true ) ) {
				/**
				 * We couldn't get the job, but there are still unreserved jobs.
				 * This shouldn't happen, so let's log an error and bail.
				 */
				new Error(
					Error::$job_retrieval_failure,
					__( 'There was an error retrieving the job while processing the queue.', 'wp-offload-ses' )
				);
			}

			return false;
		}

		// Assemble command unless job already reached current attempts limit.
		if ( $job->attempts() >= $this->attempts ) {
			$job->release();
		} else {
			try {
				$command = $job->handle();
			} catch ( Exception $exception ) {
				$job->release();
			}
		}

		if ( $job->released() && $job->attempts() >= $this->attempts ) {
			if ( empty( $exception ) ) {
				$exception = new WorkerAttemptsExceededException();
			}
			$job->fail();
		}

		// Record failed job and shortcut out.
		if ( $job->failed() ) {
			$this->connection->failure( $job, $exception );

			return true;
		}

		// Record unhandled job and shortcut out.
		if ( $job->released() ) {
			$this->connection->release( $job );

			return true;
		}

		if ( empty( $command ) ) {
			/**
			 * We couldn't get the job's command.
			 * This shouldn't happen, so let's log an error and bail.
			 */
			new Error(
				Error::$cmd_construction_failure,
				__( 'There was an error constructing the job while processing the queue.', 'wp-offload-ses' )
			);

			return false;
		}

		$this->command_pool->add_command( $command );

		return true;
	}

	/**
	 * Execute any commands already retrieved.
	 *
	 * @return void
	 */
	public function cleanup() {
		$this->command_pool->execute();
	}
}
