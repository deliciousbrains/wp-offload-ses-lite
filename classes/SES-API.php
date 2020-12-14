<?php
/**
 * Class to interact with the AWS SES API.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\Aws3\Aws\Ses\SesClient;
use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\Null_SES_Client;
use DeliciousBrains\Aws3\Aws\Ses\Exception\SesException;

/**
 * Class SES_API
 *
 * @since 1.0.0
 */
class SES_API {

	/**
	 * The SES API client.
	 *
	 * @var SesClient|Null_SES_Client
	 */
	private $client;

	/**
	 * Stores whether the access keys are valid.
	 *
	 * @var bool
	 */
	private $access_keys_check;

	/**
	 * Set up the API client.
	 *
	 * @return SesClient|Null_SES_Client
	 */
	public function get_client() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		if ( is_null( $this->client ) ) {
			$region = $wp_offload_ses->settings->get_setting( 'region', 'us-east-1' );

			$args = array(
				'region' => $region,
			);

			try {
				$this->client = $wp_offload_ses->get_aws()->get_client( $args );
			} catch ( \Exception $e ) {
				new Error( Error::$missing_access_keys, $e->getMessage() );
				$this->client = new Null_SES_Client();
			}
		}

		return $this->client;
	}

	/**
	 * Get the available regions.
	 *
	 * @return array
	 */
	public static function get_regions() {
		$regions = array(
			'us-east-1'      => __( 'US East (N. Virginia)', 'wp-offload-ses' ),
			'us-east-2'      => __( 'US East (Ohio)', 'wp-offload-ses' ),
			'us-west-1'      => __( 'US West (N. California)', 'wp-offload-ses' ),
			'us-west-2'      => __( 'US West (Oregon)', 'wp-offload-ses' ),
			'ca-central-1'   => __( 'Canada (Central)', 'wp-offload-ses' ),
			'eu-west-1'      => __( 'Europe (Ireland)', 'wp-offload-ses' ),
			'eu-west-2'      => __( 'Europe (London)', 'wp-offload-ses' ),
			'eu-west-3'      => __( 'Europe (Paris)', 'wp-offload-ses' ),
			'eu-central-1'   => __( 'Europe (Frankfurt)', 'wp-offload-ses' ),
			'eu-north-1'     => __( 'Europe (Stockholm)', 'wp-offload-ses' ),
			'ap-south-1'     => __( 'Asia Pacific (Mumbai)', 'wp-offload-ses' ),
			'ap-northeast-2' => __( 'Asia Pacific (Seoul)', 'wp-offload-ses' ),
			'ap-southeast-1' => __( 'Asia Pacific (Singapore)', 'wp-offload-ses' ),
			'ap-southeast-2' => __( 'Asia Pacific (Sydney)', 'wp-offload-ses' ),
			'ap-northeast-1' => __( 'Asia Pacific (Tokyo)', 'wp-offload-ses' ),
			'me-south-1'     => __( 'Middle East (Bahrain)', 'wp-offload-ses' ),
			'sa-east-1'      => __( 'South America (São Paulo)', 'wp-offload-ses' ),
		);

		return $regions;
	}

	/**
	 * Get the sending quota.
	 *
	 * @return array
	 */
	public function get_send_quota() {
		try {
			$quota = $this->get_client()->getSendQuota();
		} catch ( \Exception $e ) {
			$message = __( 'There was an error attempting to retrieve your SES sending limits.', 'wposes' );
			return new Error( Error::$api_get_quota, $message, $e->getMessage() );
		}

		$percentage = ( $quota['SentLast24Hours'] / $quota['Max24HourSend'] ) * 100;

		$quota['used']  = round( $percentage );
		$quota['limit'] = number_format_i18n( $quota['Max24HourSend'], 0 );
		$quota['sent']  = number_format_i18n( $quota['SentLast24Hours'], 0 );
		$quota['rate']  = number_format_i18n( $quota['MaxSendRate'], 0 );

		return $quota;
	}

	/**
	 * Get the identities associated with the account.
	 *
	 * @param array $args Args to pass to the request.
	 *
	 * @return array
	 */
	public function get_identities( $args = array() ) {
		try {
			$response   = $this->get_client()->listIdentities( $args );
			$identities = $response['Identities'];
		} catch ( \Exception $e ) {
			$message = __( 'There was an error attempting to receive your SES identities.', 'wp-offload-ses' );
			return new Error( Error::$api_get_identities, $message, $e->getMessage() );
		}

		return $identities;
	}

	/**
	 * Delete a verified identity.
	 *
	 * @param string $identity The identity to delete.
	 *
	 * @return bool
	 */
	public function delete_identity( $identity ) {
		$data = array( 'Identity' => $identity );

		try {
			$this->get_client()->deleteIdentity( $data );
		} catch ( \Exception $e ) {
			$message = __( 'There was an error deleting the provided identity', 'wp-offload-ses' );
			return new Error( Error::$api_delete_identity, $message, $e->getMessage() );
		}

		return true;
	}

	/**
	 * Get the verification status of the provided identities.
	 *
	 * @param array $identities The identities to request the status of.
	 *
	 * @return array
	 */
	public function get_identity_verification_attributes( $identities ) {
		$identities = array( 'Identities' => $identities );

		try {
			$response = $this->get_client()->getIdentityVerificationAttributes( $identities );
			$verification_attributes = $response['VerificationAttributes'];
		} catch ( \Exception $e ) {
			$message = __( 'There was an error retrieving the verification status of your SES identities.', 'wp-offload-ses' );
			return new Error( Error::$api_get_identity_verification_attributes, $message, $e->getMessage() );
		}

		return $verification_attributes;
	}

	/**
	 * Send a request to verify a domain.
	 *
	 * @param string $domain The domain to verify.
	 *
	 * @return array
	 */
	public function verify_domain( $domain ) {
		$data = array( 'Domain' => $domain );

		try {
			$response = $this->get_client()->verifyDomainIdentity( $data );
			$token    = array( 'VerificationToken' => $response['VerificationToken'] );
		} catch ( \Exception $e ) {
			$message = __( 'There was an error attempting to validate the domain.', 'wp-offload-ses' );
			return new Error( Error::$api_verify_domain, $message, $e->getMessage() );
		}

		return $token;
	}

	/**
	 * Send a request to verify an email address.
	 *
	 * @param string $email The email address to verify.
	 *
	 * @return bool
	 */
	public function verify_email_address( $email ) {
		$data = array( 'EmailAddress' => $email );

		try {
			$response = $this->get_client()->verifyEmailIdentity( $data );
		} catch ( \Exception $e ) {
			$message = __( 'There was an error attempting to validate the email address.', 'wp-offload-ses' );
			return new Error( Error::$api_verify_email, $message, $e->getMessage() );
		}

		return $response;
	}

	/**
	 * Verify that the provided region is a valid SES region.
	 *
	 * @param string $region The region to verify.
	 *
	 * @return bool
	 */
	public function validate_region( $region ) {
		return array_key_exists( $region, $this->get_regions() );
	}

	/**
	 * Send an email via SES.
	 *
	 * @param string $raw The raw email to send.
	 *
	 * @return bool
	 */
	public function send_email( $raw ) {
		$data = array(
			'RawMessage' => array(
				'Data' => $raw,
			),
		);

		try {
			$this->get_client()->sendRawEmail( $data );
		} catch ( \Exception $e ) {
			if ( $e instanceof SesException && 'MessageRejected' === $e->getAwsErrorCode() ) {
				// Handle email verification.
			}

			$message = __( 'There was an error attempting to send your email.', 'wposes' );
			return new Error( Error::$api_send, $message, $e->getMessage() );
		}

		return true;
	}

	/**
	 * Check if the provided access keys are valid.
	 *
	 * @param bool $force Force the check if already ran.
	 *
	 * @return bool
	 */
	public function check_access_keys( $force = false ) {
		/** @var WP_Offload_SES $wp_offload_ses The main plugin class. */
		global $wp_offload_ses;

		// Already checked, return the result of the previous check.
		if ( ! is_null( $this->access_keys_check ) && false === $force ) {
			return $this->access_keys_check;
		}

		// No access keys set.
		if ( $wp_offload_ses->get_aws()->needs_access_keys() ) {
			return false;
		}

		/**
		 * We have to check the send quota here becuase
		 * there is no way to verify the access keys directly.
		 */
		$quota = $this->get_send_quota();

		if ( is_wp_error( $quota ) ) {
			$error_data = $quota->get_error_data();

			// Invalid Access Key ID.
			if ( false !== strpos( $error_data, 'InvalidClientTokenId' ) ) {
				return false;
			}

			// Invalid Secret Access Key.
			if ( false !== strpos( $error_data, 'SignatureDoesNotMatch' ) ) {
				return false;
			}
		}

		return true;
	}

}
