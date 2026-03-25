<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<td colspan="2" style="padding: 10px 0;">
	<table width="100%" cellpadding="20">
		<tr>
			<td bgcolor="#eeeeee" style="background-color: #eeeeee;" align="center">
				<h3 style="font-size: 24px; font-weight: normal;"><?php esc_html_e( 'Look Ma, no failures, woohoo! 🎉', 'wp-offload-ses' ); ?></h3>
				<p>
					<?php
					echo wp_kses_post( sprintf(
						__( 'No news is good news when it comes to email sending failures.<br />Your site doesn\'t have any outstanding failures from this past %s.', 'wp-offload-ses' ),
						$this->get_health_report()->get_reporting_period()
					) );
					?>
				</p>
			</td>
		</tr>
	</table>
</td>