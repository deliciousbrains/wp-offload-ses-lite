<table width="100%" bgcolor="#34203a" style="background-color: #34203a; color: #ffffff">
	<tr>
		<td style="padding: 20px 0 20px 20px;">
			<h3 style="font-size: 24px; font-weight: normal; margin: 0; color: #ffffff;"><?php _e( 'Email Sending Health', 'wp-offload-ses' ); ?></h3>
			<p style="margin: 4px 0 0 0;"><?php echo $this->get_health_report()->get_report_date_range(); ?></p>
		</td>
		<td align="right" style="padding: 20px 20px 20px 0;">
			<table>
				<tr>
					<td style="vertical-align: middle"><?php echo $this->get_health_report()->get_plugin_logo(); ?></td>
					<td style="vertical-align: middle"><h3 style="font-size: 20px; font-weight: normal; margin: 0; color: #ffffff;"><?php echo $this->get_health_report()->get_plugin_name(); ?></h3></td>
				</tr>
			</table>
		</td>
	</tr>
</table>