<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\Command_Pool;
use DeliciousBrains\WP_Offload_SES\Error;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Exceptions\WorkerAttemptsExceededException;
use Exception;

/**
 * Class Worker
 *
 * @since 1.0.0
 */
class Worker {

	/**
	 * The AWS Command Pool wrapper.
	 *
	 * @var Command_Pool
	 */
	protected $command_pool;

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	private $connection;

	/**
	 * The number of times to attempt a job.
	 *
	 * @var int
	 */
	private $attempts;

	/**
	 * Worker constructor.
	 *
	 * @param Connection $connection The database connection.
	 * @param int        $attempts   The number of times to attempt a job.
	 */
	public function __construct( Connection $connection, int $attempts = 3 ) {
		$this->command_pool = new Command_Pool( $connection, $attempts );
		$this->connection   = $connection;
		$this->attempts     = $attempts;
	}

	/**
	 * Process a job on the queue.
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
				 * This shouldn't happen, so let's log an error and fire off the command pool just in case.
				 */
				new Error(
					Error::$job_retrieval_failure,
					__( 'There was an error retrieving the job while processing the queue.', 'wp-offload-ses' )
				);

				if ( 0 !== count( $this->command_pool->commands ) ) {
					$this->command_pool->execute();
				}
			}

			return false;
		}

		try {
			if ( $job->attempts() >= $this->attempts ) {
				$job->release();
			} else {
				$command = $job->handle();
			}
		} catch ( Exception $exception ) {
			$job->release();
		}

		if ( $job->released() && $job->attempts() >= $this->attempts ) {
			if ( empty( $exception ) ) {
				$exception = new WorkerAttemptsExceededException();
			}
			$job->fail();
		}

		if ( $job->failed() ) {
			$this->connection->failure( $job, $exception );
		} else {
			if ( ! $job->released() ) {
				if ( empty( $command ) ) {
					/**
					 * We couldn't get the job's command.
					 * This shouldn't happen, so let's log an error and fire off the command pool just in case.
					 */
					new Error(
						Error::$cmd_construction_failure,
						__( 'There was an error constructing the job while processing the queue.', 'wp-offload-ses' )
					);

					if ( 0 !== count( $this->command_pool->commands ) ) {
						$this->command_pool->execute();
					}

					return false;
				}

				$this->command_pool->add_command( $command );
			} else {
				$this->connection->release( $job );
			}
		}

		return true;
	}

}
