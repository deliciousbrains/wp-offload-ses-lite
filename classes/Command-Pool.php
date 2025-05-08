<?php
/**
 * Wrapper for the AWS command pool.
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\Aws3\Aws\Command;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\CommandPool;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\ResultInterface;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\SesV2\Exception\SesV2Exception;
use DeliciousBrains\WP_Offload_SES\Aws3\GuzzleHttp\Promise\PromiseInterface;
use DeliciousBrains\WP_Offload_SES\Queue\Connection;
use DeliciousBrains\WP_Offload_SES\Queue\Jobs\Email_Job;

/**
 * Class Command_Pool
 *
 * @since 1.0.0
 */
class Command_Pool {

	/**
	 * The number of times to attempt a job.
	 *
	 * @var int
	 */
	private $attempts;

	/**
	 * The commands to send via the AWS CommandPool.
	 *
	 * @var array
	 */
	private $commands = array();

	/**
	 * The maximum concurrency for the AWS CommandPool.
	 *
	 * Equates to the number of emails that we can send per second.
	 *
	 * @var int
	 */
	private $concurrency;

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	private $connection;

	/**
	 * How many emails have been sent within this interval.
	 *
	 * @var int
	 */
	private $send_count = 0;

	/**
	 * When did this sending interval start.
	 *
	 * @var int
	 */
	private $send_started_at = 0;

	/**
	 * Construct the Command_Pool class.
	 *
	 * @param Connection $connection The database connection.
	 * @param int        $attempts   The number of times to attempt a job.
	 */
	public function __construct( Connection $connection, int $attempts ) {
		$this->connection = $connection;
		$this->attempts   = $attempts;
	}

	/**
	 * Add a command to be run via the CommandPool.
	 *
	 * If there are no more jobs to process, or if the batch send rate has been
	 * reached, the command pool will be executed.
	 *
	 * @param Command $command The command to add.
	 */
	public function add_command( Command $command ) {
		$this->commands[] = $command;

		// Execute if we've reached our max concurrency, or if there are no more unreserved jobs.
		if ( $this->get_concurrency() <= $this->num_commands() || 0 === $this->connection->jobs( true ) ) {
			$this->execute();
		}
	}

	/**
	 * Get the maximum number of requests to be sent at once.
	 *
	 * @return int
	 */
	private function get_concurrency(): int {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		if ( ! is_null( $this->concurrency ) ) {
			return $this->concurrency;
		}

		$quota     = $wp_offload_ses->get_ses_api()->get_send_quota();
		$send_rate = 10;

		if ( ! is_wp_error( $quota ) ) {
			$send_rate = $quota['rate'];
		}

		$send_rate         = (int) apply_filters( 'wposes_max_concurrency', $send_rate );
		$this->concurrency = max( 1, min( PHP_INT_MAX, $send_rate ) );

		return $this->concurrency;
	}

	/**
	 * Create the AWS command pool and execute the commands.
	 *
	 * Does nothing if there are no commands in the pool.
	 *
	 * Empties the command pool once the commands have all attempted execution.
	 */
	public function execute() {
		if ( 0 === $this->num_commands() ) {
			return;
		}

		// Comply with SES per second rate limit.
		$this->maybe_wait_for_rate_limit_reset();

		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		// Initiate the command pool.
		$client       = $wp_offload_ses->get_ses_api()->get_client();
		$command_pool = new CommandPool( $client, $this->commands, [
			'concurrency' => $this->get_concurrency(),
			'fulfilled'   => function (
				ResultInterface $result,
				$iterKey,
				PromiseInterface $aggregatePromise
			) {
				/** @var WP_Offload_SES $wp_offload_ses */
				global $wp_offload_ses;

				$id = (int) $this->commands[ $iterKey ]['x-message-id'];
				/** @var Email_Job $job */
				$job = $this->connection->get_job( $id );

				if ( ! $job ) {
					new Error(
						Error::$job_retrieval_failure,
						__( 'Failed to retrieve the job while executing the command pool.', 'wp-offload-ses' ),
						(string) $id
					);

					return false;
				}

				$email = $wp_offload_ses->get_email_log()->get_email( $job->email_id );

				$this->connection->delete( $job );
				$wp_offload_ses->get_email_log()->update_email( $job->email_id, 'email_status', 'sent' );
				$wp_offload_ses->get_email_log()->update_email( $job->email_id, 'email_sent', current_time( 'mysql' ) );

				if ( $email ) {
					// Fires after an email has been sent.
					do_action(
						'wpses_mailsent',
						$email['email_to'],
						$email['email_subject'],
						$email['email_message'],
						$email['email_headers'],
						$email['email_attachments']
					); // Backwards compat.

					do_action(
						'wposes_mail_sent',
						$email['email_to'],
						$email['email_subject'],
						$email['email_message'],
						$email['email_headers'],
						$email['email_attachments'],
						(int) $email['email_id'],
						(int) $email['email_parent']
					);
				}

				return true;
			},
			'rejected'    => function (
				SesV2Exception $reason,
				$iterKey,
				PromiseInterface $aggregatePromise
			) {
				/** @var WP_Offload_SES $wp_offload_ses */
				global $wp_offload_ses;

				$id = (int) $this->commands[ $iterKey ]['x-message-id'];
				/** @var Email_Job $job */
				$job = $this->connection->get_job( $id );

				if ( ! $job ) {
					new Error(
						Error::$job_retrieval_failure,
						__( 'Failed to retrieve the job while executing the command pool.', 'wp-offload-ses' ),
						(string) $id
					);

					return false;
				}

				$job->release();
				$wp_offload_ses->get_email_events()->delete_links_by_email( $job->email_id );

				if ( $job->attempts() >= $this->attempts ) {
					$job->fail();
				}

				if ( $job->failed() ) {
					$this->connection->failure( $job, $reason );
				} else {
					$this->connection->release( $job );
				}

				return true;
			},
		] );

		// Send the emails in the pool.
		$promise = $command_pool->promise();
		$promise->wait();

		// One way or another we're done with the current commands.
		$this->clear();
	}

	/**
	 * How many commands are in the pool?
	 *
	 * @return int
	 */
	public function num_commands(): int {
		return count( $this->commands );
	}

	/**
	 * Empty the command pool.
	 *
	 * @return void
	 */
	public function clear() {
		$this->commands = array();
	}

	/**
	 * Determines whether the current second needs to be ticked down
	 * before another batch of items can be processed, and does so.
	 *
	 * Keeps track of how many emails have been sent in the current second,
	 * making sure that if the next batch would exceed the rate limit, then
	 * the wait happens and the count is updated appropriately.
	 *
	 * @return void
	 */
	private function maybe_wait_for_rate_limit_reset() {
		if ( ! $this->send_started() ) {
			$this->start_send();

			return;
		}

		// Update current send interval's item count.
		$this->send_count += $this->num_commands();

		if ( ! $this->rate_limit_exceeded() ) {
			return;
		}

		$this->maybe_wait_until_next_second();

		$this->start_send();
	}

	/**
	 * Have we already started a sending interval?
	 *
	 * @return bool
	 */
	private function send_started(): bool {
		return 0 !== $this->send_started_at && 0 !== $this->send_count;
	}

	/**
	 * Start a new send interval.
	 *
	 * @return void
	 */
	private function start_send(): void {
		$this->send_count      = $this->num_commands();
		$this->send_started_at = time();
	}

	/**
	 * Has the rate limit been exceeded for the current send interval?
	 *
	 * @return bool
	 */
	private function rate_limit_exceeded(): bool {
		if ( $this->get_concurrency() < $this->send_count ) {
			return true;
		}

		return false;
	}

	/**
	 * If a second hasn't ticked over since last send started, wait until it has.
	 *
	 * @return void
	 */
	private function maybe_wait_until_next_second(): void {
		$next_send_time = $this->send_started_at + 1;

		if ( time() < $next_send_time ) {
			time_sleep_until( $next_send_time );
		}
	}
}
