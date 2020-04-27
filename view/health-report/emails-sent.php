<?php
$num_sent    = $this->get_health_report()->get_total_subjects_sent();
$sent_emails = $this->get_health_report()->get_sent_emails();
?>

<table width="100%">
	<tr>
		<td>
			<h3 style="font-size: 18px; font-weight: normal; margin: 0;"><?php _e( 'Emails Sent', 'wp-offload-ses' ); ?></h3>
			<?php if ( $num_sent ): ?>
			<p style="margin: 4px 0 0 0;">
				<?php
				printf(
					__( 'Showing %1$s of %2$s different subject lines sent in the past %3$s', 'wp-offload-ses' ),
					number_format_i18n( count( $sent_emails ) ),
					number_format_i18n( $num_sent ),
					$this->get_health_report()->get_reporting_period()
				);
				?>
			</p>
			<?php endif; ?>
		</td>
		<td style="vertical-align: bottom; text-align: right" align="right">
			<?php echo $this->get_health_report()->get_view_full_report_link( 'sent' ); ?>
		</td>
	</tr>
	<tr>
		<?php
		if ( $num_sent ) {
			$this->render_view( 'health-report/sent-table', array( 'sent_emails' => $sent_emails ) );
		} else {
			$this->render_view( 'health-report/no-emails-sent' );
		}
		?>
	</tr>
</table>