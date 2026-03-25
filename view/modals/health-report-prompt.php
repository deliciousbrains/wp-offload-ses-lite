<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$upgrade_url = $this->dbrains_url(
	'/wp-offload-ses/',
	array(
		'utm_campaign' => 'WP+Offload+SES+Upgrade',
		'utm_content'  => 'health+report',
	)
);
?>

<div class="wposes-health-report-prompt" style="display: none;">

	<h3 class="wposes-upgrade-daily-reports"><?php esc_html_e( 'Upgrade for Daily Health Reports', 'wp-offload-ses' ); ?></h3>
	<h3 class="wposes-upgrade-custom-recipients"><?php esc_html_e( 'Upgrade for Custom Recipients', 'wp-offload-ses' ); ?></h3>

	<p class="wposes-upgrade-daily-reports">
		<?php
		echo wp_kses(
			sprintf(
				__( 'WP Offload SES Lite includes weekly and monthly email sending health reports. <a href="%s">Upgrade</a> to WP Offload SES for daily health reports.', 'wp-offload-ses' ),
				esc_url( $upgrade_url )
			),
			array( 'a' => array( 'href' => array() ) )
		);
		?>
	</p>

	<p class="wposes-upgrade-custom-recipients">
		<?php
		echo wp_kses(
			sprintf(
				__( 'WP Offload SES Lite includes the ability to send email sending health reports to site admins. <a href="%s">Upgrade</a> to WP Offload SES to send health reports to a list of custom recipients.', 'wp-offload-ses' ),
				esc_url( $upgrade_url )
			),
			array( 'a' => array( 'href' => array() ) )
		);
		?>
	</p>

	<button class="button button-primary wposes-modal-cancel"><?php esc_html_e( 'OK', 'wp-offload-ses' ); ?></button>

</div>
