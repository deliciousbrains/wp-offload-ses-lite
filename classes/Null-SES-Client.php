<?php
/**
 * A null SES client to use if the real one can't be initiated.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

/**
 * Class Null_SES_Client
 *
 * @since 1.0.0
 */
class Null_SES_Client {

	/**
	 * Fail calls to instance methods.
	 *
	 * @param string $name      The name of the function that was called.
	 * @param mixed  $arguments The arguements passed.
	 *
	 * @throws \Exception The SES client couldn't be instantiated.
	 */
	public function __call( $name, $arguments ) {
		throw new \Exception( 'Failed to instantiate the AWS SES client. Check your error log.' );
	}

	/**
	 * Fail calls to static methods.
	 *
	 * @param string $name      The name of the function that was called.
	 * @param mixed  $arguments The arguements passed.
	 *
	 * @throws \Exception The SES client couldn't be instantiated.
	 */
	public static function __callStatic( $name, $arguments ) {
		throw new \Exception( 'Failed to instantiate the AWS SES client. Check your error log.' );
	}

}
