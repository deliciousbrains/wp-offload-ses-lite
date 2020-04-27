<?php
$failed_emails         = $this->get_health_report()->get_failed_emails();
$num_failed            = (int) $this->get_health_report()->get_total_email_failures();
$retried_automatically = 0;
$retried_manually      = 0;

if ( $this->is_pro() ) {
	$retried_automatically = (int) $this->get_health_report()->get_total_retried_and_sent();
	$retried_manually      = (int) $this->get_health_report()->get_total_manually_retried_and_sent();
}
?>
<table width="100%">
	<tr>
		<td>
			<h3 style="margin: 10px 0 0 0; font-size: 18px; font-weight: normal;"><?php _e( 'Email Failures', 'wp-offload-ses' ); ?></h3>
			<?php if ( $num_failed ) : ?>
			<p style="margin: 4px 0 0 0;">
				<?php
				$total_failed = $num_failed + $retried_automatically + $retried_manually;
				printf(
					__( 'Showing %1$s of %2$s emails that failed to send in the past %3$s', 'wp-offload-ses' ),
					number_format_i18n( count( $failed_emails ) ),
					number_format_i18n( $total_failed ),
					$this->get_health_report()->get_reporting_period()
				);
				?>	
			</p>
			<?php endif; ?>

			<?php
			if ( $this->is_pro() ) {
				$this->render_view(
					'pro/health-report/failure-stats',
					array(
						'num_failed'            => $num_failed,
						'retried_automatically' => $retried_automatically,
						'retried_manually'      => $retried_manually,
					)
				);
			}
			?>
		</td>
		<td style="vertical-align: bottom; text-align: right;" align="right">
			<?php echo $this->get_health_report()->get_view_full_report_link( 'failed' ); ?>
		</td>
	</tr>
	<tr>
		<?php
		if ( $num_failed ) {
			$this->render_view( 'health-report/failures-table', array( 'failed_emails' => $failed_emails ) );
		} else {
			$this->render_view( 'health-report/no-failures' );
		}
		?>
	</tr>
</table>

<?php
if ( ( $num_failed || $retried_automatically ) && $this->is_pro() ) {
	$this->render_view(
		'pro/health-report/phewf',
		array(
			'num_failed'            => $num_failed,
			'retried_automatically' => $retried_automatically,
		)
	);
}
?>