<?php

if ( ! function_exists( 'wposes_check_for_upgrades' ) ) {
	/**
	 * Initialize the checking for plugin updates.
	 */
	function wposes_check_for_upgrades() {
		$properties = array(
			'plugin_slug'     => 'wp-ses',
			'plugin_basename' => plugin_basename( WPOSESLITE_FILE ),
		);

		require_once WPOSESLITE_PATH . 'ext/Plugin-Updater.php';
		new DeliciousBrains\WP_Offload_SES\Plugin_Updater( $properties );
	}

	add_action( 'admin_init', 'wposes_check_for_upgrades' );
}
