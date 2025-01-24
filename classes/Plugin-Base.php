<?php
/**
 * Base plugin class for WP Offload SES.
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

/**
 * Class Plugin_Base
 *
 * @since 1.0.0
 */
abstract class Plugin_Base {

	const DBRAINS_URL       = 'https://deliciousbrains.com';
	const WPE_URL           = 'https://wpengine.com';
	const SETTINGS_KEY      = '';
	const SETTINGS_CONSTANT = '';

	/**
	 * The plugin version.
	 *
	 * @var int
	 */
	protected $plugin_version;

	/**
	 * The plugin's name.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * The plugin basename.
	 *
	 * @var string
	 */
	protected $plugin_basename;

	/**
	 * The path to the plugin file.
	 *
	 * @var string
	 */
	protected $plugin_file_path;

	/**
	 * The path to the plugin directory.
	 *
	 * @var string
	 */
	protected $plugin_dir_path;

	/**
	 * The current page.
	 *
	 * @var string
	 */
	protected $plugin_pagenow;

	/**
	 * The default tab to load.
	 *
	 * @var string
	 */
	protected $default_tab = '';

	/**
	 * The slug for the plugin page.
	 *
	 * @var string
	 */
	protected static $plugin_page = 'wp-offload-ses';

	/**
	 * Plugin settings class.
	 *
	 * @var object
	 */
	public $settings;

	/**
	 * Construct the Plugin_Base class.
	 *
	 * @param string $plugin_file_path The path to the plugin file.
	 */
	public function __construct( $plugin_file_path ) {
		$this->plugin_file_path = $plugin_file_path;
		$this->plugin_dir_path  = $this->get_plugin_dir_path();
		$this->plugin_basename  = plugin_basename( $plugin_file_path );
		$this->plugin_pagenow   = is_network_admin() ? 'settings.php' : 'options-general.php';
		$this->settings         = new Settings( static::SETTINGS_KEY, static::SETTINGS_CONSTANT );

		if ( $this->plugin_slug && isset( $GLOBALS['wposes_meta'][ $this->plugin_slug ]['version'] ) ) {
			$this->plugin_version = $GLOBALS['wposes_meta'][ $this->plugin_slug ]['version'];
		}

		$plugin_headers = array();

		if ( is_admin() ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugin_headers = get_plugin_data( $plugin_file_path, false, false );
		}

		// Fallback to generic plugin name if it can't be retrieved from the plugin headers.
		$this->plugin_name = empty( $plugin_headers['Name'] ) ? 'WP Offload SES' : $plugin_headers['Name'];
	}

	/**
	 * Accessor for plugin version
	 *
	 * @return mixed
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	/**
	 * Accessor for plugin slug
	 *
	 * @return string
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Accessor for plugin basename
	 *
	 * @return string
	 */
	public function get_plugin_basename() {
		return $this->plugin_basename;
	}

	/**
	 * Accessor for plugin file path
	 *
	 * @return string
	 */
	public function get_plugin_file_path() {
		return $this->plugin_file_path;
	}

	/**
	 * Accessor for plugin dir path
	 *
	 * @return string
	 */
	public function get_plugin_dir_path() {
		if ( function_exists( 'wposes_get_plugin_dir_path' ) ) {
			$this->plugin_dir_path = wposes_get_plugin_dir_path();
		} else {
			$this->plugin_dir_path = wposes_lite_get_plugin_dir_path();
		}

		return $this->plugin_dir_path;
	}

	/**
	 * Accessor for plugin pagenow
	 *
	 * @return string
	 */
	public function get_plugin_pagenow() {
		return $this->plugin_pagenow;
	}

	/**
	 * Helper method to return the settings page URL for the plugin
	 *
	 * @param array  $args       Args to pass to URL.
	 * @param string $url_method To prepend to admin_url().
	 * @param bool   $escape     Should we escape the URL.
	 *
	 * @return string
	 */
	public function get_plugin_page_url( $args = array(), $url_method = 'network', $escape = true ) {
		$default_args = array(
			'page' => static::$plugin_page,
		);

		$args = array_merge( $default_args, $args );

		switch ( $url_method ) {
			case 'self':
				$base_url = self_admin_url( $this->get_plugin_pagenow() );
				break;
			default:
				$pagenow  = is_multisite() ? 'settings.php' : 'options-general.php';
				$base_url = network_admin_url( $pagenow );
		}

		// Add a hash to the URL.
		$hash = false;

		if ( isset( $args['hash'] ) ) {
			$hash = $args['hash'];
			unset( $args['hash'] );
		} elseif ( $this->default_tab ) {
			$hash = $this->default_tab;
		}

		$url = add_query_arg( $args, $base_url );

		if ( $hash ) {
			$url .= '#' . $hash;
		}

		if ( $escape ) {
			$url = esc_url_raw( $url );
		}

		return $url;
	}

	/**
	 * Enqueue script.
	 *
	 * @param string $handle The handle of the script.
	 * @param string $path   The path to the script.
	 * @param array  $deps   Script dependencies.
	 * @param bool   $footer If it should load in the footer.
	 */
	public function enqueue_script( $handle, $path, $deps = array(), $footer = true ) {
		$version = $this->get_asset_version();
		$suffix  = $this->get_asset_suffix();

		$src = $this->plugins_url( $path . $suffix . '.js' );
		wp_enqueue_script( $handle, $src, $deps, $version, $footer );
	}

	/**
	 * Enqueue style.
	 *
	 * @param string $handle Handle of the style.
	 * @param string $path   Path of the stylesheet.
	 * @param array  $deps   Stylesheet dependencies.
	 */
	public function enqueue_style( $handle, $path, $deps = array() ) {
		$version = $this->get_asset_version();

		$src = $this->plugins_url( $path . '.css' );
		wp_enqueue_style( $handle, $src, $deps, $version );
	}

	/**
	 * Wrapper for plugins_url() to account for mu-plugins.
	 *
	 * @param string $path The path to the asset.
	 *
	 * @return string
	 */
	public function plugins_url( $path ) {
		return plugins_url( $path, $this->plugin_dir_path . 'wp-offload-ses.php' );
	}

	/**
	 * Get the version used for script enqueuing
	 *
	 * @return mixed
	 */
	public function get_asset_version() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $this->plugin_version;
	}

	/**
	 * Get the filename suffix used for script enqueuing
	 *
	 * @return mixed
	 */
	public function get_asset_suffix() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}

	/**
	 * Render a view template file
	 *
	 * @param string $view View filename without the extension.
	 * @param array  $args Arguments to pass to the view.
	 */
	public function render_view( $view, $args = array() ) {
		extract( $args ); // phpcs:ignore
		include $this->plugin_dir_path . 'view/' . $view . '.php';
	}

	/**
	 * Generate site URL with correct UTM tags.
	 *
	 * @param string $path URL path.
	 * @param array  $args URL args.
	 * @param string $hash URL hash.
	 *
	 * @return string
	 */
	public function dbrains_url( $path, $args = array(), $hash = '' ) {
		$args = wp_parse_args(
			$args,
			array(
				'utm_medium' => 'insideplugin',
				'utm_source' => static::get_utm_source(),
			)
		);
		$args = array_map( 'urlencode', $args );
		$url  = trailingslashit( self::DBRAINS_URL ) . ltrim( $path, '/' );
		$url  = add_query_arg( $args, $url );

		if ( $hash ) {
			$url .= '#' . $hash;
		}

		return $url;
	}

	/**
	 * Generate WP Engine site URL with correct UTM tags.
	 *
	 * @param string $path
	 * @param array  $args
	 * @param string $hash
	 *
	 * @return string
	 */
	public static function wpe_url( $path = '', $args = array(), $hash = '' ) {
		$args = wp_parse_args( $args, array(
			'utm_medium'   => 'referral',
			'utm_source'   => 'oses_plugin',
			'utm_campaign' => 'bx_prod_referral',
		) );
		$args = array_map( 'urlencode', $args );
		$url  = trailingslashit( self::WPE_URL ) . ltrim( $path, '/' );
		$url  = add_query_arg( $args, $url );

		if ( $hash ) {
			$url .= '#' . $hash;
		}

		return $url;
	}

	/**
	 * Get UTM source for plugin.
	 *
	 * @return string
	 */
	protected static function get_utm_source() {
		return 'SES';
	}

	/**
	 * Get UTM content for WP Engine URL.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	protected static function get_wpe_url_utm_content( $content = 'plugin_footer_text' ) {
		return $content;
	}

	/**
	 * Get the My Account URL
	 *
	 * @param array  $args Optional args to add.
	 * @param string $hash Optional hash to add.
	 *
	 * @return string
	 */
	public function get_my_account_url( $args = array(), $hash = '' ) {
		return $this->dbrains_url( '/my-account/', $args, $hash );
	}

	/**
	 * Sets up hooks to alter the footer of our admin pages.
	 *
	 * @return void
	 */
	protected function init_admin_footer() {
		add_filter( 'admin_footer_text', array( $this, 'filter_admin_footer_text' ) );
		add_filter( 'update_footer', array( $this, 'filter_update_footer' ) );
	}

	/**
	 * Filters the admin footer text to add our own links.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function filter_admin_footer_text( $text ) {
		$product_link = Utils::dbrains_link(
			static::dbrains_url(
				'/wp-offload-ses/',
				array( 'utm_campaign' => 'plugin_footer', 'utm_content' => 'footer_colophon' )
			),
			$this->plugin_name
		);

		$wpe_link = Utils::dbrains_link(
			static::wpe_url(
				'',
				array( 'utm_content' => static::get_wpe_url_utm_content() )
			),
			'WP Engine'
		);

		return sprintf(
		/* translators: %1$s is a link to WP Offload SES's website, and %2$s is a link to WP Engine's website. */
			__( '%1$s is developed and maintained by %2$s.', 'wp-offload-ses' ),
			$product_link,
			$wpe_link
		);
	}

	/**
	 * Filters the admin footer's WordPress version text to add our own links.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function filter_update_footer( $content ) {
		$links[] = Utils::dbrains_link(
			static::dbrains_url(
				'/wp-offload-ses/docs/',
				array( 'utm_campaign' => 'plugin_footer', 'utm_content' => 'footer_navigation' )
			),
			__( 'Documentation', 'wp-offload-ses' )
		);

		$links[] = '<a href="' . static::get_plugin_page_url(
				array( 'hash' => 'support' )
			) . '">' . __( 'Support', 'wp-offload-ses' ) . '</a>';

		$links[] = Utils::dbrains_link(
			static::dbrains_url(
				'/wp-offload-ses/feedback/',
				array( 'utm_campaign' => 'plugin_footer', 'utm_content' => 'footer_navigation' )
			),
			__( 'Feedback', 'wp-offload-ses' )
		);

		$links[] = Utils::dbrains_link(
			static::dbrains_url(
				'/wp-offload-ses/whats-new/',
				array( 'utm_campaign' => 'plugin_footer', 'utm_content' => 'footer_navigation' )
			),
			$this->plugin_name . ' ' . $this->plugin_version,
			'whats-new'
		);

		return join( ' &#8729; ', $links );
	}
}
