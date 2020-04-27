<?php
$args          = $this->settings->get_setting_args( 'enable-health-report' );
$args['value'] = $this->settings->get_setting( 'enable-health-report' );
$style         = $args['value'] ? '' : 'style="display:none;"';
?>
<tr class="<?php echo $args['tr_class'];?>">
	<td>
		<?php $this->render_view( 'elements/checkbox', $args ); ?>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>
		<h4><?php _e( 'Email Sending Health Report', 'wp-offload-ses' ); ?></h4>
		<p>
			<?php _e( 'An emailed report summarizing recent email successes, failures, and statistics.', 'wp-offload-ses' ); ?>
		</p>

		<table id="wposes-health-report-sub-settings" <?php echo $style; ?>>
			<?php
			$args            = $this->settings->get_setting_args( 'health-report-frequency' );
			$args['options'] = $this->get_health_report()->get_available_frequencies();
			?>
			<tr class="<?php echo $args['tr_class']; ?>">
				<td><h4><?php _e( 'Frequency', 'wp-offload-ses' ); ?></h4></td>
				<td>
					<?php $this->render_view( 'elements/select', $args ); ?>
					<a class="general-helper wposes-frequency-helper" href="#"></a>
					<span class="helper-message bottom">
						<?php _e( 'Daily reports will be sent the next day. Weekly reports will send out at the beginning of the week, and monthly reports will be sent on the first day of the month.', 'wp-offload-ses' ); ?>
					</span>
				</td>
			</tr>

			<?php
			$args            = $this->settings->get_setting_args( 'health-report-recipients' );
			$recipients      = $this->settings->get_setting( 'health-report-recipients' );
			$args['value']   = $recipients;
			$args['options'] = $this->get_health_report()->get_available_recipients();
			?>

			<tr class="<?php echo $args['tr_class']; ?>">
				<td><h4><?php _e( 'Recipients', 'wp-offload-ses' ); ?></h4></td>
				<td><?php $this->render_view( 'elements/select', $args ); ?></td>
			</tr>

			<?php 
			// Only display if the recipients drop down is set to "Custom".
			$style = $args['value'] === 'custom' ? '' : 'style="display:none;"';
			$args  = $this->settings->get_setting_args( 'health-report-custom-recipients' );
			?>
			<tr class="<?php echo $args['tr_class']; ?>" <?php echo $style; ?>>
				<td></td>
				<td>
					<?php 
					if ( $this->is_pro() ) {
						$args['type']     = 'email';
						$args['multiple'] = true;

						if ( 'custom' === $recipients ) {
							$args['required'] = true;
						}

						$this->render_view( 'elements/text-field', $args );
						echo '<br>';
						_e( 'Comma separated list of email addresses.', 'wp-offload-ses' );
					} else {
						printf(
							__( '<a href="%s">Upgrade</a> to define a custom list of report recipients.', 'wp-offload-ses' ),
							$this->dbrains_url( '/wp-offload-ses/' )
						);
					}
					?>
				</td>
			</tr>
		</table>

	</td>
</tr>
