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
	public $commands = array();

	/**
	 * The maximum concurrency for the AWS CommandPool.
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
	 * @param Command $command The command to add.
	 */
	public function add_command( Command $command ) {
		$this->commands[] = $command;
		$num_commands     = count( $this->commands );

		// Execute if we've reached our max concurrency, or if there are no more unreserved jobs.
		if ( $this->get_concurrency() <= $num_commands || 0 === $this->connection->jobs( true ) ) {
			$this->execute();
			$this->commands = array();
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

		$this->concurrency = (int) apply_filters( 'wposes_max_concurrency', $send_rate );

		return $this->concurrency;
	}

	/**
	 * Create the command pool and execute the commands.
	 */
	public function execute() {
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

				if ( $email ) {
					// Fires after an email has been sent.
					do_action( 'wpses_mailsent', $email['email_to'], $email['email_subject'], $email['email_message'], $email['email_headers'], $email['email_attachments'] ); // Backwards compat.
					do_action( 'wposes_mail_sent', $email['email_to'], $email['email_subject'], $email['email_message'], $email['email_headers'], $email['email_attachments'] );
				}

				$this->connection->delete( $job );
				$wp_offload_ses->get_email_log()->update_email( $job->email_id, 'email_status', 'sent' );
				$wp_offload_ses->get_email_log()->update_email( $job->email_id, 'email_sent', current_time( 'mysql' ) );

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
	}
}
