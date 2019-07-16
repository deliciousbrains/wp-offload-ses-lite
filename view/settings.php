<?php
/* @var \WPOSES $this */
use DeliciousBrains\WP_Offload_SES\Utils;

$hide_senders       = Utils::get_first_defined_constant( array( 'WP_SES_HIDE_VERIFIED', 'WPOSES_HIDE_VERIFIED' ) );
$show_settings_tabs = $this->show_settings_tabs();

if ( $hide_senders && constant( $hide_senders ) ) {
	$hide_senders = true;
} else {
	$hide_senders = false;
}

if ( $this->is_plugin_setup() ) {
	$this->render_view( 'tabs/reports' );
	$this->render_view( 'tabs/activity' );

	if ( is_multisite() && ! is_network_admin() ) {
		$this->render_view( 'tabs/settings/network-settings' );
	}

	if ( $show_settings_tabs ) {
		$this->render_view( 'tabs/settings/general' );

		if ( ! $hide_senders ) {
			$this->render_view( 'tabs/settings/verified-senders' );
		}
	}

	$this->render_view( 'tabs/settings/send-test-email' );

	if ( $show_settings_tabs ) {
		$this->render_view( 'tabs/settings/aws-access-keys' );
	}

	if ( $this->is_pro() ) {
		$this->render_view( 'pro/tabs/settings/licence' );
	}
} else {
	$this->render_view( 'setup/start' );
	$this->render_view( 'setup/create-iam-user' );
	$this->render_view( 'setup/access-keys' );
	$this->render_view( 'setup/sandbox-mode' );
	$this->render_view( 'setup/verify-sender' );
	$this->render_view( 'setup/complete-verification' );
	$this->render_view( 'setup/configure-wp-offload-ses' );
}

if ( is_super_admin() ) {
	$this->render_view( 'tabs/support' );
}

do_action( 'wposes_after_settings' );

if ( ! $this->is_pro() ) {
	$this->render_view( 'sidebar' );
}

?>
