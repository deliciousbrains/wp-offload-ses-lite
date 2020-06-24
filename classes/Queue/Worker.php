<?php

namespace DeliciousBrains\WP_Offload_SES\Queue;

use DeliciousBrains\WP_Offload_SES\Command_Pool;

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
	public function __construct( $connection, $attempts = 3 ) {
		$this->command_pool = new Command_Pool( $connection, $attempts );
		$this->connection   = $connection;
		$this->attempts     = $attempts;
	}

	/**
	 * Process a job on the queue.
	 *
	 * @return bool
	 */
	public function process() {
		$job       = $this->connection->pop();
		$command   = null;
		$exception = null;

		if ( ! $job ) {
			return false;
		}

		try {
			if ( $job->attempts() >= $this->attempts ) {
				$job->release();
			} else {
				$command = $job->handle();
			}
		} catch ( \Exception $exception ) {
			$job->release();
		}

		if ( $job->released() && $job->attempts() >= $this->attempts ) {
			if ( empty( $exception ) ) {
				$exception = new \DeliciousBrains\WP_Offload_SES\WP_Queue\Exceptions\WorkerAttemptsExceededException();
			}
			$job->fail();
		}

		if ( $job->failed() ) {
			$this->connection->failure( $job, $exception );
		} else {
			if ( ! $job->released() ) {
				$this->command_pool->add_command( $command );
			} else {
				$this->connection->release( $job );
			}
		}

		return true;
	}

}
