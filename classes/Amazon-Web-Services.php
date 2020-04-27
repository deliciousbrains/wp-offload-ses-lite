<?php

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\Aws3\Aws\Ses\SesClient;

use Exception;

/**
 * Class Amazon_Web_Services
 *
 * @since 1.0.0
 */
class Amazon_Web_Services {

	/**
	 * The AWS client.
	 *
	 * @var object
	 */
	private $client;

	/**
	 * The main WP Offload SES class.
	 *
	 * @var WP_Offload_SES
	 */
	private $wposes;

	/**
	 * Construct the Amazon_Web_Services class.
	 *
	 * @param WP_Offload_SES $wp_offload_ses The main WP Offload SES class.
	 */
	public function __construct( $wp_offload_ses ) {
		$this->wposes = $wp_offload_ses;
	}

	/**
	 * Whether or not IAM access keys are needed.
	 *
	 * Keys are needed if we are not using EC2 roles or not defined/set yet.
	 *
	 * @return bool
	 */
	public function needs_access_keys() {
		if ( $this->use_ec2_iam_roles() ) {
			return false;
		}

		return ! $this->are_access_keys_set();
	}

	/**
	 * Check if both access key id & secret are present.
	 *
	 * @return bool
	 */
	public function are_access_keys_set() {
		return $this->get_access_key_id() && $this->get_secret_access_key();
	}

	/**
	 * Get the AWS key from a constant or the settings.
	 *
	 * Falls back to settings only if neither constant is defined.
	 *
	 * @return string
	 */
	public function get_access_key_id() {
		if ( $this->is_any_access_key_constant_defined() ) {
			$constant = $this->access_key_id_constant();

			return $constant ? constant( $constant ) : '';
		}

		return $this->wposes->settings->get_setting( 'aws-access-key-id' );
	}

	/**
	 * Get the AWS secret from a constant or the settings
	 *
	 * Falls back to settings only if neither constant is defined.
	 *
	 * @return string
	 */
	public function get_secret_access_key() {
		if ( $this->is_any_access_key_constant_defined() ) {
			$constant = $this->secret_access_key_constant();

			return $constant ? constant( $constant ) : '';
		}

		return $this->wposes->settings->get_setting( 'aws-secret-access-key' );
	}

	/**
	 * Check if any access key (id or secret, prefixed or not) is defined.
	 *
	 * @return bool
	 */
	public static function is_any_access_key_constant_defined() {
		return static::access_key_id_constant() || static::secret_access_key_constant();
	}

	/**
	 * Allows the AWS client factory to use the IAM role for EC2 instances
	 * instead of key/secret for credentials
	 * http://docs.aws.amazon.com/aws-sdk-php/guide/latest/credentials.html#instance-profile-credentials
	 *
	 * @return bool
	 */
	public function use_ec2_iam_roles() {
		$constant = $this->use_ec2_iam_role_constant();

		return $constant && constant( $constant );
	}

	/**
	 * Get the constant used to define the aws access key id.
	 *
	 * @return string|false Constant name if defined, otherwise false
	 */
	public static function access_key_id_constant() {
		return Utils::get_first_defined_constant(
			array(
				'WPOSES_AWS_ACCESS_KEY_ID',
				'DBI_AWS_ACCESS_KEY_ID',
				'AWS_ACCESS_KEY_ID',
				'WP_SES_ACCESS_KEY',
			)
		);
	}

	/**
	 * Get the constant used to define the aws secret access key.
	 *
	 * @return string|false Constant name if defined, otherwise false
	 */
	public static function secret_access_key_constant() {
		return Utils::get_first_defined_constant(
			array(
				'WPOSES_AWS_SECRET_ACCESS_KEY',
				'DBI_AWS_SECRET_ACCESS_KEY',
				'AWS_SECRET_ACCESS_KEY',
				'WP_SES_SECRET_KEY',
			)
		);
	}

	/**
	 * Get the constant used to enable the use of EC2 IAM roles.
	 *
	 * @return string|false Constant name if defined, otherwise false
	 */
	public static function use_ec2_iam_role_constant() {
		return Utils::get_first_defined_constant(
			array(
				'WPOSES_AWS_USE_EC2_IAM_ROLE',
				'DBI_AWS_USE_EC2_IAM_ROLE',
				'AWS_USE_EC2_IAM_ROLE',
			)
		);
	}

	/**
	 * Instantiate a new AWS service client for the AWS SDK
	 * using the defined AWS key and secret
	 *
	 * @param array $args The arguements needed to intiate the client.
	 *
	 * @return SesClient
	 * @throws \Exception If the access keys aren't defined.
	 */
	public function get_client( array $args ) {
		if ( $this->needs_access_keys() ) {
			throw new \Exception( sprintf( __( 'You must first <a href="%s">set your AWS access keys</a> to use this plugin.', 'wp-offload-ses' ), $this->wposes->get_plugin_page_url( array(), 'self' ) . '#settings' ) );
		}

		if ( is_null( $this->client ) ) {

			if ( ! $this->use_ec2_iam_roles() ) {
				$args = array_merge(
					array(
						'credentials' => array(
							'key'    => $this->get_access_key_id(),
							'secret' => $this->get_secret_access_key(),
						),
					),
					$args
				);
			}

			$args['version'] = '2010-12-01';
			$args            = apply_filters( 'aws_get_client_args', $args );

			$this->client = new SesClient( $args );
		}

		return $this->client;
	}

}
