<?php
/**
 * Checks compatibility for WP Offload SES.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

if ( class_exists( 'DeliciousBrains\WP_Offload_SES\Compatibility_Check' ) ) {
	return;
}

/**
 * Class Compatibility_Check
 *
 * @since 1.0.0
 */
class Compatibility_Check {

	/**
	 * The derived key of the plugin from the name, e.g. wp-offload-ses.
	 *
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * The name of the plugin, e.g. WP Offload SES.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The file path to the plugin's main file.
	 *
	 * @var string
	 */
	protected $plugin_file_path;

	/**
	 * The error message to display in the admin notice.
	 *
	 * @var string
	 */
	protected $error_message;

	/**
	 * The CSS class for the notice.
	 *
	 * @var string
	 */
	protected $notice_class = 'error';

	/**
	 * Used to store if we are installing or updating plugins once per page request.
	 *
	 * @var bool
	 */
	protected static $is_installing_or_updating_plugins;


	/**
	 * Constructs the Compatibility_Check class.
	 *
	 * @param string $plugin_name      The name of the plugin.
	 * @param string $plugin_slug      The plugin slug.
	 * @param string $plugin_file_path The path to the main plugin file.
	 */
	public function __construct( $plugin_name, $plugin_slug, $plugin_file_path ) {
		$this->plugin_name      = $plugin_name;
		$this->plugin_slug      = $plugin_slug;
		$this->plugin_file_path = $plugin_file_path;

		add_action( 'admin_notices', array( $this, 'hook_admin_notices' ) );
		add_action( 'network_admin_notices', array( $this, 'hook_admin_notices' ) );
	}

	/**
	 * Is the plugin compatible?
	 *
	 * @return bool
	 */
	public function is_compatible() {
		$compatible = $this->get_error_msg() ? false : true;

		$GLOBALS['wposes_meta'][ $this->plugin_slug ]['compatible'] = $compatible;

		return $compatible;
	}

	/**
	 * Is a plugin active
	 *
	 * @param string $plugin_base
	 *
	 * @return bool
	 */
	public function is_plugin_active( $plugin_base ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		return is_plugin_active( $plugin_base );
	}

	/**
	 * Generate a URL to perform core actions on for a plugin
	 *
	 * @param string      $action Such as activate, deactivate, install, upgrade
	 * @param string|null $basename
	 *
	 * @return string
	 */
	public function get_plugin_action_url( $action, $basename = null ) {
		if ( is_null( $basename ) ) {
			$basename = $this->get_plugin_basename();
		}

		$nonce_action = $action . '-plugin_' . $basename;
		$page         = 'plugins';

		if ( in_array( $action, array( 'upgrade', 'install' ) ) ) {
			$page   = 'update';
			$action .= '-plugin';
		}

		$url = wp_nonce_url( network_admin_url( $page . '.php?action=' . $action . '&amp;plugin=' . $basename ), $nonce_action );

		return $url;
	}

	/**
	 * Get the basename for the plugin
	 *
	 * @return string
	 */
	public function get_plugin_basename() {
		return plugin_basename( $this->plugin_file_path );
	}

	/**
	 * Set the error message to be returned for the admin notice
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	public function set_error_msg( $message ) {
		// Replace the space between the last two words with &nbsp; to prevent typographic widows
		$message = preg_replace( '/\s([\w]+[.,!\:;\\"-?]{0,1})$/', '&nbsp;\\1', $message, 1 );

		$this->error_message = $message;

		return $this->error_message;
	}

	/**
	 * Get the compatibility error message
	 *
	 * @return string|bool
	 */
	public function get_error_msg() {
		if ( ! is_null( $this->error_message ) ) {
			return $this->error_message;
		}

		$plugin_basename = $this->get_plugin_basename();
		$deactivate_url  = $this->get_plugin_action_url( 'deactivate', $plugin_basename );
		$deactivate_link = sprintf( '<a style="text-decoration:none;" href="%s">%s</a>', $deactivate_url, __( 'deactivate' ) );
		$hide_notice_msg = '<br><em>' . sprintf( __( 'You can %s the %s plugin to get rid of this notice.' ), $deactivate_link, $this->plugin_name ) . '</em>';

		// Check basic requirements for AWS SDK.
		$sdk_errors = $this->get_sdk_requirements_errors();
		if ( ! empty( $sdk_errors ) ) {
			$sdk_errors = $this->get_sdk_error_msg() . $hide_notice_msg;

			return $this->set_error_msg( $sdk_errors );
		}

		return false;
	}

	/**
	 * Check plugin capabilities for a user
	 *
	 * @return bool
	 */
	public function check_capabilities() {
		if ( is_multisite() ) {
			if ( ! current_user_can( 'manage_network_plugins' ) ) {
				return false; // Don't allow if the user can't manage network plugins
			}
		} else {
			// Don't allow if user doesn't have plugin management privileges
			$caps = array( 'activate_plugins', 'update_plugins', 'install_plugins' );
			foreach ( $caps as $cap ) {
				if ( ! current_user_can( $cap ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Display compatibility notices to users who can manage plugins
	 */
	public function hook_admin_notices() {
		if ( ! $this->check_capabilities() ) {
			return;
		}

		if ( self::is_installing_or_updating_plugins() ) {
			// Don't show notice when installing or updating plugins
			return;
		}

		$this->get_admin_notice();
	}

	/**
	 * Get the admin notice to be displayed
	 */
	public function get_admin_notice() {
		$error_msg = $this->get_error_msg();

		if ( false === $error_msg || '' === $error_msg ) {
			return;
		}

		$this->render_notice( $error_msg );
	}

	/**
	 * Render the notice HTML
	 *
	 * @param string $message
	 */
	public function render_notice( $message ) {
		printf( '<div id="wposes-compat-notice' . $this->plugin_slug . '" class="' . $this->notice_class . ' wposes-compatibility-notice"><p>%s</p></div>', $message );
	}

	/**
	 * Is the current process an install or upgrade of plugin(s)
	 *
	 * @return bool
	 */
	public static function is_installing_or_updating_plugins() {
		if ( ! is_null( self::$is_installing_or_updating_plugins ) ) {
			return self::$is_installing_or_updating_plugins;
		}

		self::$is_installing_or_updating_plugins = false;

		global $pagenow;

		if ( 'update.php' === $pagenow && isset( $_GET['action'] ) && 'install-plugin' === $_GET['action'] ) {
			// We are installing a plugin
			self::$is_installing_or_updating_plugins = true;
		}

		if ( 'plugins.php' === $pagenow && isset( $_POST['action'] ) ) {
			$action = $_POST['action'];
			if ( isset( $_POST['action2'] ) && '-1' !== $_POST['action2'] ) {
				$action = $_POST['action2'];
			}

			if ( 'update-selected' === $action ) {
				// We are updating plugins from the plugin page
				self::$is_installing_or_updating_plugins = true;
			}
		}

		if ( 'update-core.php' === $pagenow && isset( $_GET['action'] ) && 'do-plugin-upgrade' === $_GET['action'] ) {
			// We are updating plugins from the updates page
			self::$is_installing_or_updating_plugins = true;
		}

		return self::$is_installing_or_updating_plugins;
	}

	/**
	 * Checks if another version of WP Offload SES (lite/wpses) is active and deactivates it.
	 * To be hooked on `activated_plugin` so other plugin is deactivated when current plugin is activated.
	 *
	 * @param string $plugin The plugin.
	 *
	 * @return bool
	 */
	public static function deactivate_other_instances( $plugin ) {
		if ( ! in_array( basename( $plugin ), array( 'wp-offload-ses.php', 'wp-ses.php' ) ) ) {
			return false;
		}

		$plugin_to_deactivate             = 'wp-ses.php';
		$deactivated_notice_id            = '1';
		$activated_plugin_min_version     = '1.0-dev';
		$plugin_to_deactivate_min_version = '0.1';

		if ( basename( $plugin ) === $plugin_to_deactivate ) {
			$plugin_to_deactivate             = 'wp-offload-ses.php';
			$deactivated_notice_id            = '2';
			$activated_plugin_min_version     = '1.0-dev';
			$plugin_to_deactivate_min_version = '1.1-dev';
		}

		$version = self::get_plugin_version_from_basename( $plugin );

		if ( version_compare( $version, $activated_plugin_min_version, '<' ) ) {
			return false;
		}

		if ( is_multisite() ) {
			$active_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
			$active_plugins = array_keys( $active_plugins );
		} else {
			$active_plugins = (array) get_option( 'active_plugins', array() );
		}

		foreach ( $active_plugins as $basename ) {
			if ( false !== strpos( $basename, $plugin_to_deactivate ) ) {
				$version = self::get_plugin_version_from_basename( $basename );

				if ( version_compare( $version, $plugin_to_deactivate_min_version, '<' ) ) {
					return false;
				}

				set_transient( 'wposes_deactivated_notice_id', $deactivated_notice_id, HOUR_IN_SECONDS );
				deactivate_plugins( $basename );

				return true;
			}
		}

		return false;
	}

	/**
	 * Get plugin data from basename
	 *
	 * @param string $basename
	 *
	 * @return string
	 */
	public static function get_plugin_version_from_basename( $basename ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$plugin_path = WP_PLUGIN_DIR . '/' . $basename;

		// In case the plugin is installed as a MU plugin.
		$basename = explode( '/', $basename );
		$folder   = $basename[0];
		$file     = $basename[1];

		if ( file_exists( $plugin_path ) ) {
			$path = $plugin_path;
		} elseif ( file_exists( WPMU_PLUGIN_DIR . "/{$file}" ) ) {
			$path = WPMU_PLUGIN_DIR . '/' . $file;
		} elseif ( file_exists( WPMU_PLUGIN_DIR . "/$folder/$file" ) ) {
			$path = WPMU_PLUGIN_DIR . "/$folder/$file";
		} else {
			return false;
		}

		$plugin_data = get_plugin_data( $path );
		return $plugin_data['Version'];
	}

	/**
	 * Return an array of issues with the server's compatibility with the AWS SDK
	 *
	 * @return array
	 */
	public function get_sdk_requirements_errors() {
		static $errors;

		if ( ! is_null( $errors ) ) {
			return $errors;
		}

		$errors = array();

		if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {
			$errors[] = __( 'a PHP version less than 5.5', 'wp-offload-ses' );
		}

		if ( ! class_exists( '\SimpleXMLElement' ) ) {
			$errors[] = __( 'no SimpleXML PHP module', 'wp-offload-ses' );
		}

		if ( ! class_exists( '\XMLWriter' ) ) {
			$errors[] = __( 'no XMLWriter PHP module', 'wp-offload-ses' );
		}

		if ( ! function_exists( 'curl_version' ) ) {
			$errors[] = __( 'no PHP cURL library activated', 'wp-offload-ses' );

			return $errors;
		}

		if ( ! ( $curl = curl_version() ) || empty( $curl['version'] ) || empty( $curl['features'] ) || version_compare( $curl['version'], '7.16.2', '<' ) ) {
			$errors[] = __( 'a cURL version less than 7.16.2', 'wp-offload-ses' );
		}

		if ( ! empty( $curl['features'] ) ) {
			$curl_errors = array();

			if ( ! CURL_VERSION_SSL ) {
				$curl_errors[] = 'OpenSSL';
			}

			if ( ! CURL_VERSION_LIBZ ) {
				$curl_errors[] = 'zlib';
			}

			if ( $curl_errors ) {
				$errors[] = __( 'cURL compiled without', 'wp-offload-ses' ) . ' ' . implode( ' or ', $curl_errors ); // xss ok
			}
		}

		if ( ! function_exists( 'curl_multi_exec' ) ) {
			$errors[] = __( 'the function curl_multi_exec disabled', 'wp-offload-ses' );
		}

		return $errors;
	}

	/**
	 * Prepare an error message with compatibility issues
	 *
	 * @return string
	 */
	public function get_sdk_error_msg() {
		$errors = $this->get_sdk_requirements_errors();

		if ( ! $errors ) {
			return '';
		}

		$msg = __( 'The official Amazon&nbsp;Web&nbsp;Services SDK requires PHP 5.5+ with SimpleXML and XMLWriter modules, and cURL 7.16.2+ compiled with OpenSSL and zlib. Your server currently has', 'wp-offload-ses' );

		if ( count( $errors ) > 1 ) {
			$last_one = ' and ' . array_pop( $errors );
		} else {
			$last_one = '';
		}

		$msg .= ' ' . implode( ', ', $errors ) . $last_one . '.';

		return $msg;
	}

}
