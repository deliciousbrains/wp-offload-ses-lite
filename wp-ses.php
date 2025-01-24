<?php
/*
Plugin Name: WP Offload SES Lite
Description: Automatically send WordPress mail through Amazon SES (Simple Email Service).
Author: Delicious Brains
Version: 1.7.1
Author URI: https://deliciousbrains.com/
Plugin URI: https://deliciousbrains.com/
Update URI: false
Network: True
Text Domain: wp-offload-ses
Domain Path: /languages/

// Copyright (c) 2018 Delicious Brains. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************
*/

use DeliciousBrains\WP_Offload_SES\Utils;
use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\Compatibility_Check;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$GLOBALS['wposes_meta']['wp-ses']['version'] = '1.7.1';

if ( ! defined( 'WPOSESLITE_FILE' ) ) {
	// Defines the path to the main plugin file.
	define( 'WPOSESLITE_FILE', __FILE__ );

	// Defines the path to be used for includes.
	define( 'WPOSESLITE_PATH', wposes_lite_get_plugin_dir_path() );
}

if ( ! class_exists( 'DeliciousBrains\WP_Offload_SES\Compatibility_Check' ) ) {
	require_once WPOSESLITE_PATH . 'classes/Compatibility-Check.php';
}

global $wposes_compat_check;
$wposes_compat_check = new DeliciousBrains\WP_Offload_SES\Compatibility_Check(
	'WP Offload SES Lite',
	'wp-ses',
	WPOSESLITE_FILE
);

add_action( 'activated_plugin', array( $wposes_compat_check, 'deactivate_other_instances' ) );

/**
 * Initiate the WP Offload SES plugin.
 */
function wp_offload_ses_lite_init() {
	if ( class_exists( 'DeliciousBrains\WP_Offload_SES\WP_Offload_SES' ) ) {
		return;
	}

	/** @var WP_Offload_SES $wp_offload_ses */
	global $wp_offload_ses;

	/** @var Compatibility_Check $wposes_compat_check */
	global $wposes_compat_check;

	if ( $wposes_compat_check->is_plugin_active( 'wp-offload-ses/wp-offload-ses.php' ) ) {
		// Don't load if the pro version is installed.
		return;
	}

	if ( ! $wposes_compat_check->is_compatible() ) {
		return;
	}

	// Prevent error in Guzzle when PHP doesn't have the intl extension.
	if ( ! function_exists( 'idn_to_ascii' ) && ! defined( 'IDNA_DEFAULT' ) ) {
		define( 'IDNA_DEFAULT', 0 );
	}

	// Load autoloaders.
	require_once WPOSESLITE_PATH . 'vendor/Aws3/aws-autoloader.php';
	require_once WPOSESLITE_PATH . 'classes/Autoloader.php';
	new DeliciousBrains\WP_Offload_SES\Autoloader( 'WP_Offload_SES', WPOSESLITE_PATH );

	// Load compatibility functions for older PHP (< 8.0) and WordPress (< 5.9).
	require_once WPOSESLITE_PATH . 'includes/compat.php';

	// Kick off the plugin.
	$wp_offload_ses = new DeliciousBrains\WP_Offload_SES\WP_Offload_SES( WPOSESLITE_FILE );

	return $wp_offload_ses;
}

add_action( 'init', 'wp_offload_ses_lite_init', 1 );

/**
 * Gets the path to the plugin files.
 *
 * @return string
 */
function wposes_lite_get_plugin_dir_path() {
	$abspath = wp_normalize_path( dirname( WPOSESLITE_FILE ) );
	$mu_path = wp_normalize_path( WPMU_PLUGIN_DIR );

	if ( $mu_path === $abspath ) {
		$abspath = $abspath . '/wp-ses/';
	}

	return trailingslashit( $abspath );
}

/**
 * Check whether we should send mail via SES.
 *
 * @return bool
 */
function wposes_lite_sending_enabled() {
	if ( defined( 'WPOSES_SETTINGS' ) ) {
		require_once WPOSESLITE_PATH . 'classes/Utils.php';
		$defined_settings = Utils::maybe_unserialize( constant( 'WPOSES_SETTINGS' ) );

		if ( isset( $defined_settings['send-via-ses'] ) ) {
			return (bool) $defined_settings['send-via-ses'];
		}
	}

	$settings = get_option( 'wposes_settings' );

	// Single sites & multisite subsites.
	if ( isset( $settings['send-via-ses'] ) ) {
		return (bool) $settings['send-via-ses'];
	}

	// If subsite isn't configured with an override, go with network setting.
	if ( is_multisite() ) {
		$network_settings = get_site_option( 'wposes_settings' );

		if ( isset( $network_settings['send-via-ses'] ) ) {
			return (bool) $network_settings['send-via-ses'];
		}
	}

	return false;
}

// Override `wp_mail()` if sending via SES is enabled.
if ( ! function_exists( 'wp_mail' ) ) {
	if ( wposes_lite_sending_enabled() ) {
		/**
		 * Send mail via Amazon SES.
		 *
		 * @param string|array $to          Array or comma-separated list of email addresses.
		 * @param string       $subject     Email subject.
		 * @param string       $message     Email message.
		 * @param string|array $headers     Optional. Additional headers.
		 * @param string|array $attachments Optional. Files to attach.
		 *
		 * @return bool
		 */
		function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
			/** @var WP_Offload_SES $wp_offload_ses */
			global $wp_offload_ses;

			if ( is_null( $wp_offload_ses ) ) {
				$wp_offload_ses = wp_offload_ses_lite_init();
			}

			// Could not initialize plugin.
			if ( is_null( $wp_offload_ses ) ) {
				return false;
			}

			return $wp_offload_ses->mail_handler( $to, $subject, $message, $headers, $attachments );
		}
	}
} else {
	global $pagenow;

	if ( ! in_array( $pagenow, array( 'plugins.php', 'update-core.php' ), true ) ) {
		require_once WPOSESLITE_PATH . 'classes/Error.php';
		new DeliciousBrains\WP_Offload_SES\Error(
			DeliciousBrains\WP_Offload_SES\Error::$mail_function_exists,
			'Mail function already overridden.'
		);
	}
}

if ( file_exists( WPOSESLITE_PATH . 'ext/wposes-ext-functions.php' ) ) {
	require_once WPOSESLITE_PATH . 'ext/wposes-ext-functions.php';
}
