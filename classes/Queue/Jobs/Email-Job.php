<?php
/**
 * Email job that is run via cron.
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES\Queue\Jobs;

use DeliciousBrains\WP_Offload_SES\Aws3\Aws\Command;
use DeliciousBrains\WP_Offload_SES\Email;
use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\WP_Queue\Job;
use Exception;
use InvalidArgumentException;
use phpmailerException;

/**
 * Class Email_Job
 *
 * @since 1.0.0
 */
class Email_Job extends Job {

	/**
	 * The ID of the email to send.
	 *
	 * @var int
	 */
	public $email_id;

	/**
	 * The ID of the subsite sending the email.
	 *
	 * @var int
	 */
	public $subsite_id;

	/**
	 * Pass any necessary data to the job.
	 *
	 * @param int $email_id The ID of the email to send.
	 */
	public function __construct( int $email_id, $subsite_id ) {
		$this->email_id   = $email_id;
		$this->subsite_id = $subsite_id;
	}

	/**
	 * Handle the job logic.
	 *
	 * @return Command
	 *
	 * @throws phpmailerException
	 */
	public function handle(): Command {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$atts        = $wp_offload_ses->get_email_log()->get_email( $this->email_id );
		$to          = $atts['email_to'] ?? '';
		$subject     = $atts['email_subject'] ?? '';
		$message     = $atts['email_message'] ?? '';
		$headers     = $atts['email_headers'] ?? '';
		$attachments = $atts['email_attachments'] ?? array();
		$client      = $wp_offload_ses->get_ses_api()->get_client();
		$email       = new Email( $to, $subject, $message, $headers, $attachments, $this->email_id );
		$raw         = $email->prepare();

		if ( $raw instanceof Exception ) {
			throw $raw;
		}

		$data = array(
			'x-message-id' => $this->id(),
			'Content'      => array(
				'Raw' => array(
					'Data' => $raw,
				),
			),
		);

		try {
			$command = $client->getCommand( 'SendEmail', $data );
		} catch ( InvalidArgumentException $exception ) {
			throw $exception;
		}

		return $command;
	}

}
