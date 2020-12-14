<?php
/**
 * Builds an email to be sent.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\Utils;

/**
 * Class Email
 *
 * @since 1.0.0
 */
class Email {

	/**
	 * The PHPMailer class included in WordPress core.
	 *
	 * @var \PHPMailer\PHPMailer\PHPMailer 6.1.6
	 */
	private $mail;

	/**
	 * The from address.
	 *
	 * @var string
	 */
	private $from;

	/**
	 * The from name.
	 *
	 * @var string
	 */
	private $from_name;

	/**
	 * The charset to use in the email.
	 *
	 * @var string
	 */
	private $charset;

	/**
	 * The content type of the email.
	 *
	 * @var string
	 */
	private $content_type;

	/**
	 * The ID of the email.
	 *
	 * @var int|null
	 */
	private $email_id;

	/**
	 * Init WP_Offload_SES_Email class
	 *
	 * @param string|array $to          The recipient of the email.
	 * @param string       $subject     The subject of the email.
	 * @param string       $message     The email message.
	 * @param string|array $headers     Headers to include in the email.
	 * @param string|array $attachments Attachments to include in the email.
	 * @param int|null     $email_id    The ID of the email.
	 */
	public function __construct( $to, $subject, $message, $headers, $attachments, $email_id = null ) {
		$this->mail     = $this->get_PHPMailer();
		$this->email_id = $email_id;

		$this->to( $to );
		$this->subject( $subject );
		$this->body( $message );
		$this->headers( $headers );
		$this->attachments( $attachments );
	}

	/**
	 * Gets the PHP Mailer instance.
	 * 
	 * Backwards-compatibility for pre-5.5 versions of WordPress.
	 *
	 * @return PHPMailer
	 */
	public function get_PHPMailer() {
		if ( file_exists( ABSPATH . WPINC . '/PHPMailer/PHPMailer.php' ) ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
			$PHPMailer = new \PHPMailer\PHPMailer\PHPMailer();
		} else {
			require_once ABSPATH . WPINC . '/class-phpmailer.php';
			$PHPMailer = new \PHPMailer( true );
		}

		return $PHPMailer;
	}

	/**
	 * Maybe convert to array
	 *
	 * @param string|array $value The value to process.
	 *
	 * @return array
	 */
	private function maybe_convert_to_array( $value ) {
		if ( ! is_array( $value ) ) {
			return explode( ',', $value );
		}

		return $value;
	}

	/**
	 * Maybe split recipient
	 *
	 * @param string $recipient the recipient to process.
	 *
	 * @return array
	 */
	private function maybe_split_recipient( $recipient ) {
		// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>".
		$recipient_name = '';

		if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
			if ( 3 === count( $matches ) ) {
				$recipient_name = $matches[1];
				$recipient      = $matches[2];
			}
		}

		return array(
			'name'  => $recipient_name,
			'email' => $recipient,
		);
	}

	/**
	 * Maybe log an unverified sender.
	 *
	 * @param string $email The email address to add if unverified.
	 */
	private function maybe_log_unverified_sender( $email ) {
		global $wp_offload_ses;

		if ( ! $wp_offload_ses->is_verified_email_address( $email ) ) {
			$unverified_senders = get_transient( 'wposes_unverified_senders' );

			if ( false === $unverified_senders ) {
				$unverified_senders = array();
			}

			if ( ! in_array( $email, $unverified_senders, true ) ) {
				$unverified_senders[] = $email;
				set_transient( 'wposes_unverified_senders', $unverified_senders );
			}
		}
	}

	/**
	 * Set to
	 *
	 * @param string|array $to The recipient of the email.
	 */
	private function to( $to ) {
		$to = $this->maybe_convert_to_array( $to );

		foreach ( $to as $recipient ) {
			try {
				$recipient = $this->maybe_split_recipient( $recipient );
				$this->mail->AddAddress( $recipient['email'], $recipient['name'] );
			} catch ( \Exception $e ) {
				continue;
			}
		}
	}

	/**
	 * Set from
	 */
	private function from() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$wp_default_email = Utils::get_wordpress_default_email( false );

		if ( is_null( $this->from ) || $wp_default_email === $this->from ) {

			if ( false !== $wp_offload_ses->settings->get_setting( 'default-email', false ) ) {
				$this->from = $wp_offload_ses->settings->get_setting( 'default-email' );
			} else {
				$this->from = $wp_default_email;
			}
		}

		if ( is_null( $this->from_name ) ) {
			$this->from_name = 'WordPress';

			if ( false !== $wp_offload_ses->settings->get_setting( 'default-email-name', false ) ) {
				$this->from_name = $wp_offload_ses->settings->get_setting( 'default-email-name' );
			}
		}

		$this->mail->From     = apply_filters( 'wp_mail_from', $this->from );
		$this->mail->FromName = trim( apply_filters( 'wp_mail_from_name', $this->from_name ), '" ' );

		// Log the email address if it isn't verified.
		$this->maybe_log_unverified_sender( $this->mail->From );
	}

	/**
	 * Set subject
	 *
	 * @param string $subject The subject of the email.
	 */
	private function subject( $subject ) {
		$this->mail->Subject = $subject;
	}

	/**
	 * Set body
	 *
	 * @param string $body The body of the email.
	 */
	private function body( $body ) {
		$this->mail->Body = $body;
	}

	/**
	 * Set headers
	 *
	 * @param string|array $headers Headers to include in the email.
	 */
	private function headers( $headers ) {
		if ( empty( $headers ) ) {
			return;
		}

		// Handle newline-delimited string list of headers.
		if ( ! is_array( $headers ) ) {
			$headers = trim( str_replace( "\r\n", "\n", $headers ) );
			$headers = explode( "\n", $headers );
		}

		foreach ( $headers as $header ) {
			$header = explode( ':', trim( $header ), 2 );

			if ( ! is_array( $header ) || 2 !== count( $header ) ) {
				continue;
			}

			list( $name, $content ) = $header;
			$name    = trim( $name );
			$content = trim( $content );
			$this->handle_headers( $name, $content );
		}
	}

	/**
	 * Handle headers
	 *
	 * @param string $name    The name of the header.
	 * @param string $content The header content.
	 */
	private function handle_headers( $name, $content ) {
		switch ( strtolower( $name ) ) {
			case 'from':
				$this->header_from( $content );
				break;
			case 'content-type':
				$this->header_content_type( $content );
				break;
			case 'cc':
				$this->header_cc( $content, 'cc' );
				break;
			case 'bcc':
				$this->header_cc( $content, 'bcc' );
				break;
			case 'reply-to':
				$this->header_reply_to( $content );
				break;
			default:
				if ( 'mime-version' === strtolower( $name ) ) {
					// PHPMailer adds mime-version, skip
					break;
				}

				$this->mail->addCustomHeader( $name, $content );
		}
	}

	/**
	 * Header from
	 *
	 * @param string $content The from header content.
	 */
	private function header_from( $content ) {
		$recipient       = $this->maybe_split_recipient( $content );
		$this->from      = $recipient['email'];
		$this->from_name = $recipient['name'];
	}

	/**
	 * Header content type
	 *
	 * @param string $content Header content.
	 */
	private function header_content_type( $content ) {
		if ( false !== strpos( $content, ';' ) ) {
			list( $content_type, $charset ) = explode( ';', $content );
			$this->content_type = trim( $content_type );

			if ( false !== stripos( $charset, 'charset=' ) ) {
				$this->charset = trim( str_replace( array( 'charset=', '"' ), '', $charset ) );
			} elseif ( false !== stripos( $content_type, 'multipart' ) && false !== stripos( $charset, 'boundary=' ) ) {
				$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset ) );
				$this->mail->AddCustomHeader( sprintf( "Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary ) );
			}
		} elseif ( '' !== trim( $content ) ) {
			$this->content_type = trim( $content );
		}
	}

	/**
	 * Header Cc
	 *
	 * @param string $content Header content.
	 * @param string $type    Cc by default.
	 */
	private function header_cc( $content, $type = 'cc' ) {
		$recipient = $this->maybe_split_recipient( $content );

		try {
			switch ( $type ) {
				case 'cc':
					$this->mail->addCC( $recipient['email'], $recipient['name'] );
					break;
				case 'bcc':
					$this->mail->addBCC( $recipient['email'], $recipient['name'] );
					break;
			}
		} catch ( \Exception $e ) {
			return;
		}
	}

	/**
	 * Header Reply-To
	 *
	 * @param string $content Header content.
	 */
	private function header_reply_to( $content ) {
		$reply_to = $this->maybe_convert_to_array( $content );

		foreach ( $reply_to as $recipient ) {
			try {
				$recipient = $this->maybe_split_recipient( $recipient );
				$this->mail->addReplyTo( $recipient['email'], $recipient['name'] );
			} catch ( \Exception $e ) {
				continue;
			}
		}
	}

	/**
	 * Set attachments
	 *
	 * @param string|array $attachments Attachments to add.
	 */
	private function attachments( $attachments ) {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$stored_attachments = array();

		if ( null !== $this->email_id ) {
			$stored_attachments = $wp_offload_ses->get_attachments()->get_attachments_by_email( $this->email_id );
		}

		// Support the legacy method of adding attachments.
		if ( empty( $stored_attachments ) && ! empty( $attachments ) ) {
			// Handle newline-delimited string list of multiple file names
			if ( ! is_array( $attachments ) ) {
				$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
			}

			foreach ( $attachments as $attachment ) {
				$stored_attachments[] = array( 'path' => $attachment, 'filename' => wp_basename( $attachment ) );
			}
		}

		foreach ( $stored_attachments as $attachment ) {
			try {
				$this->mail->AddAttachment( $attachment['path'], $attachment['filename'] );
			} catch( \Exception $e ) {
				continue;
			}
		}
	}

	/**
	 * Set content type
	 */
	private function content_type() {
		if ( is_null( $this->content_type ) ) {
			$this->content_type = apply_filters( 'wp_mail_content_type', 'text/plain' );
		}

		$this->mail->ContentType = $this->content_type;

		if ( 'text/html' === $this->mail->ContentType ) {
			$this->mail->IsHTML( true );
		}
	}

	/**
	 * Set charset
	 */
	private function charset() {
		if ( is_null( $this->charset ) ) {
			$this->charset = get_bloginfo( 'charset' );
		}

		$this->mail->CharSet = apply_filters( 'wp_mail_charset', $this->charset );
	}

	/**
	 * Sets the Sender/return path.
	 */
	private function return_path() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$return_path = $wp_offload_ses->settings->get_setting( 'return-path', false );

		if ( ! $return_path ) {
			$return_path = $this->mail->From;
		}

		$this->mail->addCustomHeader( 'Return-Path', $return_path );
	}

	/**
	 * Maybe override the Reply-To header.
	 */
	private function maybe_override_reply_to() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$reply_to = $wp_offload_ses->settings->get_setting( 'reply-to', '' );

		if ( '' !== $reply_to && 'headers' !== $reply_to ) {
			$this->mail->clearReplyTos();
			$this->mail->addReplyTo( $reply_to );
		}
	}

	/**
	 * Prepare the email for sending.
	 *
	 * Use PHPMailer to generate correct email format
	 *
	 * @return string
	 * @throws \phpmailerException
	 */
	public function prepare() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$this->from();
		$this->content_type();
		$this->charset();
		$this->return_path();
		$this->maybe_override_reply_to();

		if ( null !== $this->email_id && 'text/html' === $this->mail->ContentType ) {
			$this->mail->Body = make_clickable( $this->mail->Body );
			$this->mail->Body = $wp_offload_ses->get_email_events()->filter_email_content( $this->email_id, $this->mail->Body );
		}

		// Fires after PHPMailer is initalized.
		do_action_ref_array( 'phpmailer_init', array( &$this->mail ) );

		try {
			$this->mail->preSend();
		} catch ( \PHPMailer\PHPMailer\Exception $exception ) {
			return $exception;
		} catch ( \phpmailerException $exception ) {
			return $exception;
		} catch ( \Exception $exception ) {
			return $exception;
		}

		return $this->mail->getSentMIMEMessage();
	}

	/**
	 * View the email. Returns HTML used in modal.
	 *
	 * @param array $email_data Additional info about the email.
	 *
	 * @return void
	 */
	public function view( $email_data = array() ) {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$this->from();
		$this->content_type();
		$this->charset();
		$this->return_path();

		$to          = implode( ', ',  array_keys( $this->mail->getAllRecipientAddresses() ) );
		$opens       = '';
		$clicks      = '';
		$status      = '';
		$sent        = '';
		$attachments = '';
		$actions     = $wp_offload_ses->get_email_action_links( $email_data['id'], $email_data['status'] );
		unset( $actions['view'] );

		// Maybe add HTML line breaks.
		if ( 'text/html' !== $this->mail->ContentType ) {
			$this->mail->Body = nl2br( $this->mail->Body );
		}

		if ( isset( $email_data['status_i18n'] ) ) {
			$status = sprintf( __( 'Status: %s', 'wp-offload-ses' ), $email_data['status_i18n'] );
		}

		if ( isset( $email_data['sent'] ) && $email_data['sent'] ) {
			$formatted = Utils::get_date_and_time( $email_data['sent'] );
			$sent      = sprintf( __( 'Sent: %1$s at %2$s', 'wp-offload-ses' ), $formatted['date'], $formatted['time'] );
		}

		if ( isset( $email_data['open_count'] ) && (int) $email_data['open_count'] ) {
			$formatted = Utils::get_date_and_time( $email_data['last_opened'] );

			if ( $formatted ) {
				$opens = sprintf(
					__( 'Opens: %1$d (Last Opened %2$s at %3$s)', 'wp-offload-ses' ),
					$email_data['open_count'],
					$formatted['date'],
					$formatted['time']
				);
			}
		}

		if ( isset( $email_data['click_count'] ) && (int) $email_data['click_count'] ) {
			$formatted = Utils::get_date_and_time( $email_data['last_clicked'] );

			if ( $formatted ) {
				$clicks = sprintf(
					__( 'Clicks: %1$d (Last Clicked %2$s at %3$s)', 'wp-offload-ses' ),
					$email_data['click_count'],
					$formatted['date'],
					$formatted['time']
				);
			}
		}

		$attachment_links = $wp_offload_ses->get_attachments()->get_attachment_links( $email_data['id'] );
		if ( ! empty( $attachment_links ) ) {
			$attachments =  '<hr />' . _n( 'Attachment: ', 'Attachments: ', count( $attachment_links ), 'wp-offload-ses' ) . implode( ', ', $attachment_links );
		}
		?>
		<div id="wposes-email-wrap">
			<h3 id="wposes-email-subject"><?php echo esc_html( $this->mail->Subject ); ?></h3>
			<span id="wposes-email-from"><?php printf( __( 'From: %1$s &lt;%2$s&gt;', 'wp-offload-ses' ), $this->mail->FromName, $this->mail->From ); ?></span>
			<span id="wposes-email-to"><?php printf( __( 'To: %s', 'wp-offload-ses' ), $to ); ?></span>
			<span id="wposes-email-sent"><?php echo $sent; ?></span>

			<div id="wposes-email-content"><?php echo $this->mail->Body; ?></div>

			<span id="wposes-email-attachments"><?php echo $attachments; ?></span>

			<div class="actions select">
				<span id="wposes-email-info" style="float: left;">
					<span id="wposes-email-status"><?php echo $status; ?></span>
					<span id="wposes-email-opens"><?php echo $opens; ?></span>
					<span id="wposes-email-clicks"><?php echo $clicks; ?></span>
				</span>

				<span id="wposes-email-actions" style="float: right">
					<?php echo implode( ' | ', $actions ); ?>
				</span>
			</div>
		</div>
		<?php
	}

}
