<?php
/*
Plugin Name: WP Offload SES Lite
Description: Automatically send WordPress mail through Amazon SES (Simple Email Service).
Author: Delicious Brains
Version: 1.1
Author URI: https://deliciousbrains.com/
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

// Exit if accessed directly.
use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\Compatibility_Check;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$GLOBALS['wposes_meta']['wp-offload-ses-lite']['version'] = '1.1';

if ( ! class_exists( 'DeliciousBrains\WP_Offload_SES\Compatibility_Check' ) ) {
	require_once dirname( __FILE__ ) . '/classes/Compatibility-Check.php';
}

global $wposes_compat_check;
$wposes_compat_check = new DeliciousBrains\WP_Offload_SES\Compatibility_Check(
	'WP SES',
	'wp-ses',
	__FILE__
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

	$abspath = dirname( __FILE__ );
	if ( WPMU_PLUGIN_DIR === $abspath ) {
		$abspath = $abspath . '/wp-offload-ses/';
	}

	if ( $wposes_compat_check->is_plugin_active( 'wp-offload-ses/wp-offload-ses.php' ) ) {
		// Don't load if the pro version is installed.
		return;
	}

	if ( ! $wposes_compat_check->is_compatible() ) {
		return;
	}

	// Load autoloaders.
	require_once $abspath . '/vendor/Aws3/aws-autoloader.php';
	require_once $abspath . '/classes/Autoloader.php';
	new DeliciousBrains\WP_Offload_SES\Autoloader( 'WP_Offload_SES', $abspath );

	// Kick off the plugin.
	$wp_offload_ses = new DeliciousBrains\WP_Offload_SES\WP_Offload_SES( __FILE__ );

	return $wp_offload_ses;
}
add_action( 'init', 'wp_offload_ses_lite_init', 1 );

/**
 * Check whether we should send mail via SES.
 *
 * @return bool
 */
function wposes_lite_sending_enabled() {
	if ( defined( 'WPOSES_SETTINGS' ) ) {
		$defined_settings = maybe_unserialize( constant( 'WPOSES_SETTINGS' ) );

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

/*
 * Override wp_mail if SES enabled
 */
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

			return $wp_offload_ses->mail_handler( $to, $subject, $message, $headers, $attachments );
		}
	}
} else {
	global $pagenow;

	if ( ! in_array( $pagenow, array( 'plugins.php', 'update-core.php' ), true ) ) {
		require_once dirname( __FILE__ ) . '/classes/Error.php';
		new DeliciousBrains\WP_Offload_SES\Error( DeliciousBrains\WP_Offload_SES\Error::$mail_function_exists, 'Mail function already overridden.' );
	}
}
