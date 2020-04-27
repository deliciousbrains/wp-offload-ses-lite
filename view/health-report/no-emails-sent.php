<td colspan="2" style="padding: 10px 0;">
	<table width="100%" cellpadding="20">
		<tr>
			<td bgcolor="#eeeeee" style="background-color: #eeeeee;" align="center">
				<h3 style="font-size: 24px; font-weight: normal;"><?php _e( 'No Emails Sent', 'wp-offload-ses' ); ?></h3>
				<p>
					<?php
					printf(
						__( 'Your site hasn\'t sent any emails this past %s.<br />If this is unusual you should probably check if your site is broken.', 'wp-offload-ses' ),
						$this->get_health_report()->get_reporting_period()
					);
					?>
				</p>
			</td>
		</tr>
	</table>
</td>