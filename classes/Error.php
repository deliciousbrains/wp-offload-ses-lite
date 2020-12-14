<?php
/**
 * Extension of WP_Error for WP Offload SES.
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

/**
 * Class Error
 *
 * @since 1.0.0
 */
class Error extends \WP_Error {

	// Generic errors.
	public static $mail_function_exists = 101;
	public static $missing_access_keys = 102;

	// API related errors.
	public static $api_get_quota = 201;
	public static $api_send = 202;
	public static $api_get_identities = 203;
	public static $api_verify_domain = 204;
	public static $api_verify_email = 205;
	public static $api_delete_identity = 206;
	public static $api_get_identity_verification_attributes = 207;

	// License related errors.
	public static $licence_error = 301;

	// Cron related errors.
	public static $send_cron_email = 401;
	public static $job_retrieval_failure = 402;

	/**
	 * Error constructor.
	 *
	 * @param string|int       $code  $error Error code.
	 * @param string|\WP_Error $error Error message or instance of WP_Error.
	 * @param mixed            $data  Optional. Error data.
	 */
	public function __construct( $code = '', $error = '', $data = '' ) {
		if ( is_wp_error( $error ) ) {
			$message = $error->get_error_code() . ': ' . $error->get_error_message();
			$data    = ( $data ) ? $data : $error->get_error_data();
		} else {
			$message = $error;
		}

		// Debug log.
		$this->log_error( $code, $message, $data );

		parent::__construct( $code, $message, $data );
	}

	/**
	 * Log error
	 *
	 * @param string       $code    The error code to log.
	 * @param string       $message The message to log.
	 * @param string|array $data    Any relevant data for the error.
	 */
	private function log_error( $code, $message, $data ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

			if ( ! empty( $data ) && is_string( $data ) ) {
				$message .= ' (' . $data . ')';
			}

			error_log( 'WP Offload SES #' . $code . ': ' . $message ); // phpcs:ignore

			if ( is_array( $data ) || is_object( $data ) ) {
				error_log( print_r( $data, true ) ); // phpcs:ignore
			}
		}
	}

}
