<div id="tab-network-settings" data-prefix="wposes" class="wposes-tab wposes-content">

	<h3><?php _e( 'Network Settings', 'wp-offload-ses' ); ?></h3>

	<form id="wposes-network-settings-form" method="post">
		<input type="hidden" name="action" value="save_override_network_settings" />
		<input type="hidden" name="plugin" value="<?php echo $this->get_plugin_slug(); ?>" />
		<input type="hidden" name="completed-setup" value="1" />
		<?php wp_nonce_field( $this->get_settings_nonce_key(), 'wposes_override_network_settings' ); ?>

		<table class="form-table">
			<?php $args = $this->settings->get_setting_args( 'override-network-settings' ); ?>
			<tr class="<?php echo $args['tr_class'];?>">
				<td>
					<?php $this->render_view( 'elements/checkbox', $args ); ?>
				</td>
				<td>
					<?php echo $args['setting_msg']; ?>
					<h4><?php _e( 'Override Network Settings', 'wp-offload-ses' ); ?></h4>
					<p>
						<?php _e( 'Override the below network settings for this subsite.', 'wp-offload-ses' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<table id="wposes-network-settings-table" class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php _e( 'Network Setting', 'wp-offload-ses' ); ?></th>
					<th><?php _e( 'Value', 'wp-offload-ses' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php _e( 'Send via SES', 'wp-offload-ses' ); ?></td>
					<td>
						<?php
						if ( $this->settings->get_network_setting( 'send-via-ses', false ) ) {
							echo '<span class="dashicons dashicons-yes"></span>';
						} else {
							echo '<span class="dashicons dashicons-no-alt"></span>';
						}
						?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Enable Open Tracking', 'wp-offload-ses' ); ?></td>
					<td>
					<?php
					if ( $this->settings->get_network_setting( 'enable-open-tracking', false ) ) {
						echo '<span class="dashicons dashicons-yes"></span>';
					} else {
						echo '<span class="dashicons dashicons-no-alt"></span>';
					}
					?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Enable Click Tracking', 'wp-offload-ses' ); ?></td>
					<td>
					<?php
					if ( $this->settings->get_network_setting( 'enable-click-tracking', false ) ) {
						echo '<span class="dashicons dashicons-yes"></span>';
					} else {
						echo '<span class="dashicons dashicons-no-alt"></span>';
					}
					?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Email Sending Health Report', 'wp-offload-ses' ); ?></td>
					<td>
					<?php
					if ( $this->settings->get_network_setting( 'enable-health-report', false ) ) {
						$hr_enabled = true;
						echo '<span class="dashicons dashicons-yes"></span>';
					} else {
						$hr_enabled = false;
						echo '<span class="dashicons dashicons-no-alt"></span>';
					}
					?>
					</td>
				</tr>
				<?php if ( $hr_enabled ) : ?>
					<tr>
						<td><?php _e( 'Health Report Frequency', 'wp-offload-ses' ); ?></td>
						<td>
						<?php
						$frequency   = $this->settings->get_network_setting( 'health-report-frequency', 'weekly' );
						$frequencies = $this->get_health_report()->get_available_frequencies();

						if ( isset( $frequencies[ $frequency ] ) ) {
							echo $frequencies[ $frequency ];
						}
						?>
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Health Report Recipients', 'wp-offload-ses' ); ?></td>
						<td>
						<?php
						$custom_recipients = false;
						if ( 'custom' === $this->settings->get_network_setting( 'health-report-recipients', 'custom' ) ) {
							$custom_recipients = true;
							_e( 'Custom', 'wp-offload-ses' );
						} else {
							_e( 'Site Admins', 'wp-offload-ses' );
						}
						?>
						</td>
					</tr>
					<?php if ( $custom_recipients ) : ?>
						<tr>
							<td><?php _e( 'Health Report Custom Recipients', 'wp-offload-ses' ); ?></td>
							<td>
								<?php echo esc_html( $this->settings->get_network_setting( 'health-report-custom-recipients', '' ) ); ?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				<tr>
					<td><?php _e( 'Region', 'wp-offload-ses' ); ?></td>
					<td>
						<?php echo esc_html( $this->settings->get_network_setting( 'region', '' ) ); ?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'WordPress Notification Email', 'wp-offload-ses' ); ?></td>
					<td>
						<?php echo esc_html( $this->settings->get_network_setting( 'default-email', '' ) ); ?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'WordPress Notification Name', 'wp-offload-ses' ); ?></td>
					<td>
						<?php echo esc_html( $this->settings->get_network_setting( 'default-email-name', '' ) ); ?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Reply To', 'wp-offload-ses' ); ?></td>
					<td>
						<?php echo esc_html( $this->settings->get_network_setting( 'reply-to', '' ) ); ?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Return Path', 'wp-offload-ses' ); ?></td>
					<td>
						<?php echo esc_html( $this->settings->get_network_setting( 'return-path', '' ) ); ?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Delete Logs', 'wp-offload-ses' ); ?></td>
					<td>
						<?php printf( __( '%d days', 'wp-offload-ses' ), (int) $this->settings->get_network_setting( 'log-duration', 90 ) ); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<button type="submit" class="button button-primary"><?php _e( 'Save changes', 'wp-offload-ses' ); ?></button>

	</form>
</div>
