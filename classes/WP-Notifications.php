<?php
/**
 * Filters the default WordPress notifications.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;

/**
 * Class WP_Notifications
 *
 * @since 1.0.0
 */
class WP_Notifications {

	/**
	 * Construct the WP_Notifications class.
	 *
	 * @param WP_Offload_SES $wp_offload_ses The main plugin instance.
	 */
	public function __construct( $wp_offload_ses ) {
		$open_tracking  = $wp_offload_ses->settings->get_setting( 'enable-open-tracking' );
		$click_tracking = $wp_offload_ses->settings->get_setting( 'enable-click-tracking' );

		if ( ! $open_tracking && ! $click_tracking ) {
			// No point in filtering the emails if they aren't being tracked.
			return;
		}

		// New comment/ping back notifications.
		add_filter( 'comment_notification_text', array( $this, 'convert_text_to_html' ) );
		add_filter( 'comment_notification_headers', array( $this, 'convert_headers_to_html' ) );
		add_filter( 'comment_moderation_text', array( $this, 'convert_text_to_html' ) );
		add_filter( 'comment_moderation_headers', array( $this, 'convert_headers_to_html' ) );

		// Password change notification.
		add_filter( 'wp_password_change_notification_email', array( $this, 'convert_mail_to_html' ) );
		add_filter( 'retrieve_password_message', array( $this, 'convert_text_to_html' ) );

		/**
		 * WordPress doesn't currently have a filter for
		 * changing the headers of password retrieval emails.
		 */
		add_action( 'lostpassword_post', array( $this, 'manually_set_content_type' ) );

		// New user notification.
		add_filter( 'wp_new_user_notification_email_admin', array( $this, 'convert_mail_to_html' ) );
		add_filter( 'wp_new_user_notification_email', array( $this, 'convert_mail_to_html' ) );
	}

	/**
	 * Changes the content type when WordPress doesn't provide a filter.
	 */
	public function manually_set_content_type() {
		add_filter(
			'wp_mail_content_type',
			function( $content_type ) {
				return 'text/html';
			}
		);
	}

	/**
	 * Converts the provided mail to HTML.
	 *
	 * @param array $mail The array of mail properties.
	 *
	 * @return array
	 */
	public function convert_mail_to_html( $mail ) {
		if ( isset( $mail['message'] ) ) {
			$mail['message'] = $this->convert_text_to_html( $mail['message'] );
		}

		if ( isset( $mail['headers'] ) ) {
			$mail['headers'] = $this->convert_headers_to_html( $mail['headers'] );
		}

		return $mail;
	}

	/**
	 * Converts the provided message to HTML.
	 *
	 * @param string $body The plaintext body to convert.
	 *
	 * @return string
	 */
	public function convert_text_to_html( $body ) {
		$body = nl2br( make_clickable( $body ) );

		// Deal with invalid HTML this creates with some default WP emails.
		$body = str_replace( '<<a href=', '<a href=', $body );
		$body = str_replace( '</a>>', '</a>', $body );

		return $body;
	}

	/**
	 * Converts the headers from plain/text to text/html
	 *
	 * @param string $headers The headers to convert.
	 *
	 * @return string
	 */
	public function convert_headers_to_html( $headers ) {
		/**
		 * We can just add the content type to the end
		 * since it will override any previous content types.
		 */
		if ( is_array( $headers ) ) {
			$headers[] = 'Content-Type: text/html;';
		} else {
			$headers = "Content-Type: text/html;\n" . $headers;
		}

		return $headers;
	}

}
