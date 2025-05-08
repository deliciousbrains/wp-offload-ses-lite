<?php
/**
 * Class to interact with the AWS SES API.
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\Aws3\Aws\SesV2\Exception\SesV2Exception;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\SesV2\SesV2Client;
use Exception;

/**
 * Class SES_API
 *
 * @since 1.0.0
 */
class SES_API {

	/**
	 * The SES API client.
	 *
	 * @var SesV2Client|Null_SES_Client
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
	 * @return SesV2Client|Null_SES_Client
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
			} catch ( Exception $e ) {
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
	public static function get_regions(): array {
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
			'eu-south-1'     => __( 'Europe (Milan)', 'wp-offload-ses' ),
			'af-south-1'     => __( 'Africa (Cape Town)', 'wp-offload-ses' ),
			'ap-southeast-3' => __( 'Asia Pacific (Jakarta)', 'wp-offload-ses' ),
			'ap-south-1'     => __( 'Asia Pacific (Mumbai)', 'wp-offload-ses' ),
			'ap-northeast-3' => __( 'Asia Pacific (Osaka)', 'wp-offload-ses' ),
			'ap-northeast-2' => __( 'Asia Pacific (Seoul)', 'wp-offload-ses' ),
			'ap-southeast-1' => __( 'Asia Pacific (Singapore)', 'wp-offload-ses' ),
			'ap-southeast-2' => __( 'Asia Pacific (Sydney)', 'wp-offload-ses' ),
			'ap-northeast-1' => __( 'Asia Pacific (Tokyo)', 'wp-offload-ses' ),
			'il-central-1'   => __( 'Israel (Tel Aviv)', 'wp-offload-ses' ),
			'me-south-1'     => __( 'Middle East (Bahrain)', 'wp-offload-ses' ),
			'sa-east-1'      => __( 'South America (São Paulo)', 'wp-offload-ses' ),
		);

		return apply_filters( 'wposes_ses_regions', $regions );
	}

	/**
	 * Get the account.
	 *
	 * @return array|Error
	 */
	public function get_account() {
		static $account;

		if ( ! empty( $account ) ) {
			return $account;
		}

		try {
			$account = $this->get_client()->getAccount();
		} catch ( Exception $e ) {
			$message = __( 'There was an error attempting to retrieve your SES account details.', 'wposes' );

			return new Error( Error::$api_get_account, $message, $e->getMessage() );
		}

		return $account;
	}

	/**
	 * Get the sending quota.
	 *
	 * Returns an array in the form:
	 *
	 * [
	 *   'used'  => float,  // Percentage of the quota used over the last 24 hours
	 *   'limit' => string, // The maximum number of emails that can be sent in a 24 hour period
	 *   'sent'  => string, // The number of emails sent in the last 24 hours
	 *   'rate'  => string, // The maximum number of emails that can be sent per second
	 * ]
	 *
	 * Returns an OSES Error object if the quotas could not be retrieved.
	 *
	 * @return array|Error
	 */
	public function get_send_quota() {
		$account = $this->get_account();

		if ( is_wp_error( $account ) ) {
			return $account;
		}

		if (
			! isset( $account['SendQuota']['Max24HourSend'] ) ||
			! isset( $account['SendQuota']['MaxSendRate'] ) ||
			! isset( $account['SendQuota']['SentLast24Hours'] )
		) {
			$message = __( 'There was an error attempting to retrieve your SES sending limits.', 'wposes' );

			return new Error( Error::$api_get_quota, $message );
		}

		$quota      = $account['SendQuota'];
		$percentage = ( $quota['SentLast24Hours'] / $quota['Max24HourSend'] ) * 100;

		$quota['used']  = round( $percentage );
		$quota['limit'] = number_format_i18n( $quota['Max24HourSend'] );
		$quota['sent']  = number_format_i18n( $quota['SentLast24Hours'] );
		$quota['rate']  = number_format_i18n( $quota['MaxSendRate'] );

		return $quota;
	}

	/**
	 * Get the identities associated with the account.
	 *
	 * @param array $args            Args to pass to the request.
	 * @param array $prev_identities Previously returned identities if paging.
	 *
	 * @return array|Error
	 */
	public function get_identities( array $args = array(), array $prev_identities = array() ) {
		if ( empty( $args['PageSize'] ) ) {
			$args['PageSize'] = 1000;
		}

		$prev_identities = empty( $prev_identities ) ? array() : $prev_identities;

		try {
			$response   = $this->get_client()->listEmailIdentities( $args );
			$identities = $response['EmailIdentities'];
		} catch ( Exception $e ) {
			$message = __( 'There was an error attempting to receive your SES identities.', 'wp-offload-ses' );

			return new Error( Error::$api_get_identities, $message, $e->getMessage() );
		}

		// Decorate domain identities pending verification with more details not supplied by list command.
		foreach ( $identities as &$identity ) {
			if ( 'EMAIL_ADDRESS' === $identity['IdentityType'] || 'PENDING' !== $identity['VerificationStatus'] ) {
				continue;
			}

			$details = $this->get_identity_details( $identity['IdentityName'] );

			// Skip further processing of identity if any issues, this data is not critical.
			if ( is_wp_error( $details ) ) {
				continue;
			}

			// Grab first Verification Token.
			if ( ! empty( $details['DkimAttributes']['Tokens'][0] ) ) {
				$identity['VerificationTokens'] = $details['DkimAttributes']['Tokens'];
			}
		}

		$identities = array_merge( $prev_identities, $identities );

		if ( ! empty( $response['NextToken'] ) ) {
			$args['NextToken'] = $response['NextToken'];

			return $this->get_identities( $args, $identities );
		}

		return $identities;
	}

	/**
	 * Delete a verified identity.
	 *
	 * @param string $identity The identity to delete.
	 *
	 * @return bool|Error
	 */
	public function delete_identity( string $identity ) {
		$data = array( 'EmailIdentity' => $identity );

		try {
			$this->get_client()->deleteEmailIdentity( $data );
		} catch ( Exception $e ) {
			$message = __( 'There was an error deleting the provided identity.', 'wp-offload-ses' );

			return new Error( Error::$api_delete_identity, $message, $e->getMessage() );
		}

		return true;
	}

	/**
	 * Get details for the provided identity.
	 *
	 * @param string $identity The identity to get details for.
	 *
	 * @return array|Error
	 */
	public function get_identity_details( string $identity ) {
		try {
			$response = $this->get_client()->getEmailIdentity( array( 'EmailIdentity' => $identity ) );
		} catch ( Exception $e ) {
			$message = sprintf(
				__( 'There was an error retrieving the details of your "%s" SES identity.', 'wp-offload-ses' ),
				$identity
			);

			return new Error( Error::$api_get_identity_details, $message, $e->getMessage() );
		}

		return $response;
	}

	/**
	 * Send a request to verify a domain.
	 *
	 * @param string $domain The domain to verify.
	 *
	 * @return array|Error
	 */
	public function verify_domain( string $domain ) {
		$data = array( 'EmailIdentity' => $domain );

		try {
			$response = $this->get_client()->createEmailIdentity( $data );
			$tokens   = array( 'VerificationTokens' => $response['DkimAttributes']['Tokens'] );
		} catch ( SesV2Exception $e ) {
			return new Error( Error::$api_verify_domain, $e->getAwsErrorMessage() );
		} catch ( Exception $e ) {
			$message = __( 'There was an error attempting to validate the domain.', 'wp-offload-ses' );

			return new Error( Error::$api_verify_domain, $message, $e->getMessage() );
		}

		return $tokens;
	}

	/**
	 * Send a request to verify an email address.
	 *
	 * @param string $email The email address to verify.
	 *
	 * @return bool|Error
	 */
	public function verify_email_address( string $email ) {
		$data = array( 'EmailIdentity' => $email );

		try {
			$response = $this->get_client()->createEmailIdentity( $data );
		} catch ( SesV2Exception $e ) {
			return new Error( Error::$api_verify_email, $e->getAwsErrorMessage() );
		} catch ( Exception $e ) {
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
	public function validate_region( string $region ): bool {
		return array_key_exists( $region, $this->get_regions() );
	}

	/**
	 * Send an email via SES.
	 *
	 * @param string $raw The raw email to send.
	 *
	 * @return bool|Error
	 */
	public function send_email( string $raw ) {
		$data = array(
			'Content' => array(
				'Raw' => array(
					'Data' => $raw,
				),
			),
		);

		try {
			$this->get_client()->sendEmail( $data );
		} catch ( Exception $e ) {
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
	public function check_access_keys( bool $force = false ): bool {
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
		 * We have to check the account here because
		 * there is no way to verify the access keys directly.
		 */
		$account = $this->get_account();

		if ( is_wp_error( $account ) ) {
			$error_data = $account->get_error_data();

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
