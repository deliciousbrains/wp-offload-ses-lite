<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<table width="100%" bgcolor="#34203a" style="background-color: #34203a; color: #ffffff">
	<tr>
		<td width="350" style="padding: 20px 0 20px 20px;">
			<p style="font-size: 10px;"><?php
				echo esc_html( sprintf(
					__( 'You received this email because the Email Sending Health Report is enabled in your %s settings. Simply turn it off to stop these emails.', 'wp-offload-ses' ),
					$this->get_health_report()->get_plugin_name()
				) );
				?>
			</p>
		</td>
		<td align="right" style="padding: 20px 20px 20px 0;">
			<table>
				<tr>
					<td style="vertical-align: middle"><?php echo wp_kses_post( $this->get_health_report()->get_plugin_logo() ); ?></td>
					<td style="vertical-align: middle"><h3 style="font-size: 20px; font-weight: normal; margin: 0; color: #ffffff;"><?php echo esc_html( $this->get_health_report()->get_plugin_name() ); ?></h3></td>
				</tr>
			</table>
		</td>
	</tr>
</table>