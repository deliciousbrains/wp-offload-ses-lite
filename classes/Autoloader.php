<?php
/**
 * Autoloader for WP Offload SES.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

/**
 * Class Autoloader
 *
 * @since 1.0.0
 */
class Autoloader {

	/**
	 * Path to the main plugin file.
	 *
	 * @var string
	 */
	protected $abspath;

	/**
	 * Prefix to use in namespaces.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Vendor to use in namespaces.
	 *
	 * @var string
	 */
	protected $vendor = 'DeliciousBrains';

	/**
	 * Autoloader constructor.
	 *
	 * @param string $prefix  Prefix to use in namespaces.
	 * @param string $abspath Path to the main plugin file.
	 *
	 * @throws \Exception
	 */
	public function __construct( $prefix, $abspath ) {
		$this->prefix  = $prefix;
		$this->abspath = $abspath;

		$this->register_autoloader();
	}

	/**
	 * Registers the autoloader.
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function register_autoloader() {
		return spl_autoload_register( array( $this, 'autoloader' ) );
	}

	/**
	 * Load the classes.
	 *
	 * @param string $class_name The class to load.
	 */
	public function autoloader( $class_name ) {
		if ( ! $this->class_belongs_to_plugin( $class_name ) ) {
			return;
		}

		$class_path = $this->get_class_path( $class_name );
		$path       = $this->get_classes_directory() . $class_path;

		if ( file_exists( $path ) ) {
			require $path;
		} else {
			$path = $this->get_classes_directory( true ) . $class_path;

			if ( file_exists( $path ) ) {
				require $path;
			}
		}
	}

	/**
	 * Class belong to plugin.
	 *
	 * @param string $class_name The class name.
	 *
	 * @return bool
	 */
	protected function class_belongs_to_plugin( $class_name ) {
		if ( 0 !== strpos( $class_name, $this->vendor . '\\' . $this->prefix . '\\' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get class path.
	 *
	 * @param string $class_name The class name.
	 *
	 * @return string
	 */
	protected function get_class_path( $class_name ) {
		$parts = explode( '\\', $class_name );
		$parts = array_slice( $parts, 2 );

		$filename = implode( DIRECTORY_SEPARATOR, $parts ) . '.php';

		return str_replace( '_', '-', $filename );
	}

	/**
	 * Get classes directory.
	 *
	 * @param bool $vendor If the path should include the vendor directory.
	 *
	 * @return string
	 */
	protected function get_classes_directory( $vendor = false ) {
		$dir = $vendor ? 'vendor' : 'classes';
		return $this->abspath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
	}

}
