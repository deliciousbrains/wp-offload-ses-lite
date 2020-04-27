<?php
/**
 * Creates the diagnostic information for WP Offload SES.
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\Queue\Queue_Status;

/**
 * Class Diagnostic_Info
 *
 * @since 1.0.0
 */
class Diagnostic_Info {

	/**
	 * Diagnostic information for the support tab
	 *
	 * @param bool $escape If some content should be escaped.
	 *
	 * @return string
	 */
	public function output_diagnostic_info( $escape = true ) {
		global $table_prefix;
		global $wpdb;
		global $wp_offload_ses;

		$output = 'site_url(): ';
		$output .= esc_html( site_url() );
		$output .= "\r\n";

		$output .= 'home_url(): ';
		$output .= esc_html( home_url() );
		$output .= "\r\n";

		$output .= 'Web Server: ';
		$output .= esc_html( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '' );
		$output .= "\r\n";

		$output .= 'WordPress: ';
		$output .= get_bloginfo( 'version', 'display' );
		if ( is_multisite() ) {
			$output .= ' Multisite ';
			$output .= '(' . ( is_subdomain_install() ? 'subdomain' : 'subdirectory' ) . ')';
			$output .= "\r\n";
			$output .= 'Multisite Site Count: ';
			$output .= esc_html( get_blog_count() );
			$output .= "\r\n";
			$output .= 'Domain Mapping: ' . ( defined( 'SUNRISE' ) && SUNRISE ? 'Enabled' : 'Disabled' );
		}
		$output .= "\r\n";

		$output .= 'WP Locale: ';
		$output .= esc_html( get_locale() );
		$output .= "\r\n";

		$output .= 'PHP: ';
		if ( function_exists( 'phpversion' ) ) {
			$output .= esc_html( phpversion() );
		}
		$output .= "\r\n";

		$output .= 'PHP Memory Limit: ';
		if ( function_exists( 'ini_get' ) ) {
			$output .= esc_html( ini_get( 'memory_limit' ) );
		}
		$output .= "\r\n";

		$output .= 'WP Memory Limit: ';
		$output .= esc_html( WP_MEMORY_LIMIT );
		$output .= "\r\n";

		$output .= 'Memory Usage: ';
		$output .= size_format( memory_get_usage( true ) );
		$output .= "\r\n";

		$output .= 'WP Max Upload Size: ';
		$output .= esc_html( size_format( wp_max_upload_size() ) );
		$output .= "\r\n";

		$output .= 'PHP Time Limit: ';
		if ( function_exists( 'ini_get' ) ) {
			$output .= esc_html( ini_get( 'max_execution_time' ) );
		}
		$output .= "\r\n";

		$output .= 'PHP Error Log: ';
		if ( function_exists( 'ini_get' ) ) {
			$output .= esc_html( ini_get( 'error_log' ) );
		}
		$output .= "\r\n";

		$output .= 'MySQL: ';
		$output .= esc_html( $wpdb->db_version() );
		$output .= "\r\n";

		$output .= 'Database Name: ';
		$output .= esc_html( $wpdb->dbname );
		$output .= "\r\n";

		$output .= 'Table Prefix: ';
		$output .= esc_html( $table_prefix );
		$output .= "\r\n";

		$output .= 'ext/mysqli: ';
		$output .= empty( $wpdb->use_mysqli ) ? 'no' : 'yes';
		$output .= "\r\n";

		$output .= 'cURL: ';
		if ( function_exists( 'curl_init' ) ) {
			$curl   = curl_version();
			$output .= esc_html( $curl['version'] );
		} else {
			$output .= 'Disabled';
		}
		$output .= "\r\n";

		$output .= 'OpenSSL: ';
		if ( $this->open_ssl_enabled() ) {
			$output .= esc_html( OPENSSL_VERSION_TEXT );
		} else {
			$output .= 'Disabled';
		}
		$output .= "\r\n";

		$output .= 'fsockopen: ';
		if ( function_exists( 'fsockopen' ) ) {
			$output .= 'Enabled';
		} else {
			$output .= 'Disabled';
		}
		$output .= "\r\n";

		$output          .= 'allow_url_fopen: ';
		$allow_url_fopen = ini_get( 'allow_url_fopen' );
		if ( empty( $allow_url_fopen ) ) {
			$output .= 'Disabled';
		} else {
			$output .= 'Enabled';
		}
		$output .= "\r\n";

		$output .= 'Zlib Compression: ';
		if ( function_exists( 'gzcompress' ) ) {
			$output .= 'Enabled';
		} else {
			$output .= 'Disabled';
		}
		$output .= "\r\n";

		$output .= 'Basic Auth: ';
		if ( isset( $_SERVER['REMOTE_USER'] ) || isset( $_SERVER['PHP_AUTH_USER'] ) || isset( $_SERVER['REDIRECT_REMOTE_USER'] ) ) {
			$output .= 'Enabled';
		} else {
			$output .= 'Disabled';
		}
		$output .= "\r\n";

		$output .= 'Proxy: ';
		if ( defined( 'WP_PROXY_HOST' ) || defined( 'WP_PROXY_PORT' ) ) {
			$output .= 'Enabled';
		} else {
			$output .= 'Disabled';
		}
		$output .= "\r\n";

		$output .= 'Blocked External HTTP Requests: ';
		if ( ! defined( 'WP_HTTP_BLOCK_EXTERNAL' ) || ! WP_HTTP_BLOCK_EXTERNAL ) {
			$output .= 'None';
		} else {
			$accessible_hosts = ( defined( 'WP_ACCESSIBLE_HOSTS' ) ) ? WP_ACCESSIBLE_HOSTS : '';

			if ( empty( $accessible_hosts ) ) {
				$output .= 'ALL';
			} else {
				$output .= 'Partially (Accessible Hosts: ' . esc_html( $accessible_hosts ) . ')';
			}
		}

		$output .= "\r\n\r\n";

		$output .= 'Send Mail Using SES: ';
		$output .= $this->on_off( 'send-via-ses' );
		$output .= "\r\n";

		$output .= 'Enable Open Tracking: ';
		$output .= $this->on_off( 'enable-open-tracking' );
		$output .= "\r\n";

		$output .= 'Enable Click Tracking: ';
		$output .= $this->on_off( 'enable-click-tracking' );
		$output .= "\r\n";

		$output .= 'Region: ';
		$output .= $wp_offload_ses->settings->get_setting( 'region' );
		$output .= "\r\n";

		$output .= 'Log Duration: ';
		$output .= (int) $wp_offload_ses->get_email_log()->get_log_duration();
		$output .= "\r\n";

		$queue_status = new Queue_Status( $wp_offload_ses );

		$output .= "\r\n";
		$output .= 'WP Cron: ';
		$output .= esc_html( $queue_status->is_wp_cron_enabled() ? 'Enabled' : 'Disabled' );
		$output .= "\r\n";

		$output .= 'Alternate WP Cron: ';
		$output .= esc_html( $queue_status->is_alternate_wp_cron() ? 'Enabled' : 'Disabled' );
		$output .= "\r\n";

		$output .= 'Last Run: ';
		$output .= $this->time_from_timestamp( $queue_status->get_last_cron_run() );
		$output .= "\r\n";

		$output .= 'Next Scheduled: ';
		$output .= $this->time_from_timestamp( $queue_status->get_next_scheduled() );
		$output .= "\r\n";

		$output .= 'Queued: ';
		$output .= $queue_status->get_total_jobs();
		$output .= "\r\n";

		$output .= 'Failures: ';
		$output .= $queue_status->get_total_failures();
		$output .= "\r\n\r\n";

		if ( $wp_offload_ses->is_pro() ) {
			$output .= 'License: ';
			$output .= $wp_offload_ses->is_valid_licence() ? 'Valid' : 'Not Valid';	
			$output .= "\r\n";
		}

		$output .= 'WPOSES_SETTINGS: ';
		$output .= esc_html( ( defined( 'WPOSES_SETTINGS' ) ) ? 'Defined' : 'Not defined' );
		$output .= "\r\n";

		$output .= 'WPOSES_LICENCE: ';
		$output .= esc_html( ( defined( 'WPOSES_LICENCE' ) ) ? 'Defined' : 'Not defined' );
		$output .= "\r\n";

		$output .= 'AWS_USE_EC2_IAM_ROLE: ';
		$output .= esc_html( ( defined( 'AWS_USE_EC2_IAM_ROLE' ) ) ? AWS_USE_EC2_IAM_ROLE : 'Not defined' );

		$output .= "\r\n\r\n";

		$output .= 'WP_DEBUG: ';
		$output .= esc_html( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Yes' : 'No' );
		$output .= "\r\n";

		$output .= 'WP_DEBUG_LOG: ';
		$output .= esc_html( ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) ? 'Yes' : 'No' );
		$output .= "\r\n";

		$output .= 'WP_DEBUG_DISPLAY: ';
		$output .= esc_html( ( defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) ? 'Yes' : 'No' );
		$output .= "\r\n";

		$output .= 'SCRIPT_DEBUG: ';
		$output .= esc_html( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'Yes' : 'No' );
		$output .= "\r\n";

		$output .= 'WP_CONTENT_DIR: ';
		$output .= esc_html( ( defined( 'WP_CONTENT_DIR' ) ) ? WP_CONTENT_DIR : 'Not defined' );
		$output .= "\r\n";

		$output .= 'WP_CONTENT_URL: ';
		$output .= esc_html( ( defined( 'WP_CONTENT_URL' ) ) ? WP_CONTENT_URL : 'Not defined' );
		$output .= "\r\n";

		$output .= 'UPLOADS: ';
		$output .= esc_html( ( defined( 'UPLOADS' ) ) ? UPLOADS : 'Not defined' );
		$output .= "\r\n";

		$output .= 'WP_PLUGIN_DIR: ';
		$output .= esc_html( ( defined( 'WP_PLUGIN_DIR' ) ) ? WP_PLUGIN_DIR : 'Not defined' );
		$output .= "\r\n";

		$output .= 'WP_PLUGIN_URL: ';
		$output .= esc_html( ( defined( 'WP_PLUGIN_URL' ) ) ? WP_PLUGIN_URL : 'Not defined' );
		$output .= "\r\n";

		$output = apply_filters( 'wposes_diagnostic_info', $output );
		if ( has_action( 'wposes_diagnostic_info' ) ) {
			$output .= "\r\n";
		}

		$theme_info = wp_get_theme();
		$output     .= "\r\n";
		$output     .= "Active Theme Name: " . esc_html( $theme_info->get( 'Name' ) );
		$output     .= "\r\n";
		$output     .= "Active Theme Version: " . esc_html( $theme_info->get( 'Version' ) );
		$output     .= "\r\n";
		$output     .= "Active Theme Folder: " . esc_html( $theme_info->get_stylesheet() );
		$output     .= "\r\n";

		if ( is_child_theme() ) {
			$parent_info = $theme_info->parent();
			$output      .= "Parent Theme Name: " . esc_html( $parent_info->get( 'Name' ) );
			$output      .= "\r\n";
			$output      .= "Parent Theme Version: " . esc_html( $parent_info->get( 'Version' ) );
			$output      .= "\r\n";
			$output      .= "Parent Theme Folder: " . esc_html( $parent_info->get_stylesheet() );
			$output      .= "\r\n";
		}
		if ( ! file_exists( $theme_info->get_stylesheet_directory() ) ) {
			$output .= "WARNING: Active Theme Folder Not Found\r\n";
		}

		$output .= "\r\n";

		$output         .= "Active Plugins:\r\n";
		$active_plugins = (array) get_option( 'active_plugins', array() );
		$plugin_details = array();

		if ( is_multisite() ) {
			$network_active_plugins = wp_get_active_network_plugins();
			$active_plugins         = array_map( array( $this, 'remove_wp_plugin_dir' ), $network_active_plugins );
		}

		foreach ( $active_plugins as $plugin ) {
			$plugin_details[] = $this->get_plugin_details( WP_PLUGIN_DIR . '/' . $plugin );
		}

		asort( $plugin_details );
		$output .= implode( '', $plugin_details );

		$mu_plugins = wp_get_mu_plugins();
		if ( $mu_plugins ) {
			$mu_plugin_details = array();
			$output            .= "\r\n";
			$output            .= "Must-use Plugins:\r\n";

			foreach ( $mu_plugins as $mu_plugin ) {
				$mu_plugin_details[] = $this->get_plugin_details( $mu_plugin );
			}

			asort( $mu_plugin_details );
			$output .= implode( '', $mu_plugin_details );
		}

		$dropins = get_dropins();
		if ( $dropins ) {
			$output .= "\r\n\r\n";
			$output .= "Drop-ins:\r\n";

			foreach ( $dropins as $file => $dropin ) {
				$output .= $file . ( isset( $dropin['Name'] ) ? ' - ' . $dropin['Name'] : '' );
				$output .= "\r\n";
			}
		}

		return $output;
	}

	/**
	 * Helper to remove the plugin directory from the plugin path
	 *
	 * @param string $path The path to remove the dir from.
	 *
	 * @return string
	 */
	public function remove_wp_plugin_dir( $path ) {
		$plugin_dir_path = trailingslashit( WP_PLUGIN_DIR );
		$plugin          = str_replace( $plugin_dir_path, '', $path );

		return $plugin;
	}

	/**
	 * Helper to display plugin details
	 *
	 * @param string $plugin_path Plugin file path.
	 * @param string $suffix      Plugin suffix.
	 *
	 * @return string
	 */
	public function get_plugin_details( $plugin_path, $suffix = '' ) {
		$plugin_data = get_plugin_data( $plugin_path );
		if ( empty( $plugin_data['Name'] ) ) {
			return basename( $plugin_path );
		}

		return sprintf( "%s%s (v%s) by %s\r\n", $plugin_data['Name'], $suffix, $plugin_data['Version'], strip_tags( $plugin_data['AuthorName'] ) );
	}

	/**
	 * Helper for displaying settings
	 *
	 * @param string $key Setting key.
	 *
	 * @return string
	 */
	public function on_off( $key ) {
		/* @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$value = $wp_offload_ses->settings->get_setting( $key, 0 );

		return ( 1 == $value ) ? 'On' : 'Off';
	}

	/**
	 * Helper for showing time/date from timestamps.
	 *
	 * @param string $timestamp The timestamp.
	 *
	 * @return string
	 */
	public function time_from_timestamp( $timestamp ) {
		if ( ! $timestamp ) {
			return 'N/A';
		}

		$time_format = 'H:i:s Y-m-d';

		return date_i18n( $time_format, $timestamp ) . ' UTC';
	}

	/**
	 * Detect if OpenSSL is enabled
	 *
	 * @return bool
	 */
	public function open_ssl_enabled() {
		if ( defined( 'OPENSSL_VERSION_TEXT' ) ) {
			return true;
		} else {
			return false;
		}
	}

}
