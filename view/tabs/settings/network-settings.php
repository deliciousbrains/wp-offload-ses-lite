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

		<?php $network_settings = $this->settings->get_network_settings(); ?>

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
						if ( isset( $network_settings['send-via-ses'] ) && (bool) $network_settings['send-via-ses'] ) {
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
					if ( isset( $network_settings['enable-open-tracking'] ) && (bool) $network_settings['enable-open-tracking'] ) {
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
					if ( isset( $network_settings['enable-click-tracking'] ) && (bool) $network_settings['enable-click-tracking'] ) {
						echo '<span class="dashicons dashicons-yes"></span>';
					} else {
						echo '<span class="dashicons dashicons-no-alt"></span>';
					}
					?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Region', 'wp-offload-ses' ); ?></td>
					<td>
						<?php
						if ( isset( $network_settings['region'] ) ) {
							echo esc_html( $network_settings['region'] );
						}
						?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'WordPress Notification Email', 'wp-offload-ses' ); ?></td>
					<td>
						<?php
						if ( isset( $network_settings['default-email'] ) ) {
							echo esc_html( $network_settings['default-email'] );
						}
						?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'WordPress Notification Name', 'wp-offload-ses' ); ?></td>
					<td>
						<?php
						if ( isset( $network_settings['default-email-name'] ) ) {
							echo esc_html( $network_settings['default-email-name'] );
						}
						?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Reply To', 'wp-offload-ses' ); ?></td>
					<td>
						<?php
						if ( isset( $network_settings['reply-to'] ) ) {
							echo esc_html( $network_settings['reply-to'] );
						}
						?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Return Path', 'wp-offload-ses' ); ?></td>
					<td>
						<?php
						if ( isset( $network_settings['return-path'] ) ) {
							echo esc_html( $network_settings['return-path'] );
						}
						?>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Delete Logs', 'wp-offload-ses' ); ?></td>
					<td>
						<?php printf( __( '%d days', 'wp-offload-ses' ), (int) $network_settings['log-duration'] ); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<button type="submit" class="button button-primary"><?php _e( 'Save changes', 'wp-offload-ses' ); ?></button>

	</form>
</div>
