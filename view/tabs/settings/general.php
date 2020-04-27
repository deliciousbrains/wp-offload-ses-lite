<div id="tab-general" data-prefix="wposes" class="wposes-tab wposes-content">

	<h3><?php _e( 'General Settings', 'wp-offload-ses' ); ?></h3>

	<form id="wposes-settings-form" method="post">
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="plugin" value="<?php echo $this->get_plugin_slug(); ?>" />
		<input type="hidden" name="completed-setup" value="1" />

		<?php
			wp_nonce_field( $this->get_settings_nonce_key(), 'wposes_save_settings' );
			do_action( 'wposes_form_hidden_fields' );
		?>

		<table class="form-table">

			<?php
			if ( is_multisite() && is_network_admin() ) {
				$this->render_view( 'settings/enable-subsite-settings' );
			}
			?>

			<?php $args = $this->settings->get_setting_args( 'send-via-ses' ); ?>
			<tr class="<?php echo $args['tr_class'];?>">
				<td>
					<?php $this->render_view( 'elements/checkbox', $args ); ?>
				</td>
				<td>
					<?php echo $args['setting_msg']; ?>
					<h4><?php _e( 'Send Mail Using SES', 'wp-offload-ses' ); ?></h4>
					<p>
						<?php _e( 'Route all outgoing emails through SES.', 'wp-offload-ses' ); ?>
					</p>
				</td>
			</tr>

			<?php $args = $this->settings->get_setting_args( 'enable-open-tracking' ); ?>
			<tr class="<?php echo $args['tr_class'];?>">
				<td>
					<?php $this->render_view( 'elements/checkbox', $args ); ?>
				</td>
				<td>
					<?php echo $args['setting_msg']; ?>
					<h4><?php _e( 'Enable Open Tracking', 'wp-offload-ses' ); ?></h4>
					<p>
						<?php _e( 'Log when an email is opened.', 'wp-offload-ses' ); ?>
						<a class="general-helper" href="#"></a>
						<span class="helper-message bottom">
							<?php
							_e( 'When enabled, WP Offload SES will insert a transparent 1x1 pixel into emails so email opens can be tracked.', 'wp-offload-ses' );

							if ( ! $this->is_pro() ) {
								_e( ' Upgrade to WP Offload SES Pro to view open tracking reports.', 'wp-offload-ses' );
							}
							?>
						</span>
					</p>
				</td>
			</tr>

			<?php $args = $this->settings->get_setting_args( 'enable-click-tracking' ); ?>
			<tr class="<?php echo $args['tr_class'];?>">
				<td>
					<?php $this->render_view( 'elements/checkbox', $args ); ?>
				</td>
				<td>
					<?php echo $args['setting_msg']; ?>
					<h4><?php _e( 'Enable Click Tracking', 'wp-offload-ses' ); ?></h4>
					<p>
						<?php _e( 'Log any links clicked in emails.', 'wp-offload-ses' ); ?>
						<a class="general-helper" href="#"></a>
						<span class="helper-message bottom">
							<?php
							_e( 'When enabled, WP Offload SES will alter the links in emails sent so that clicks can be tracked before redirecting the recipient to the original link destination.', 'wp-offload-ses' );

							if ( ! $this->is_pro() ) {
								_e( ' Upgrade to WP Offload SES Pro to view click tracking reports.', 'wp-offload-ses' );
							}
							?>
						</span>
					</p>
				</td>
			</tr>

			<?php $this->render_view( 'settings/health-report' ); ?>

			<?php
				$this->render_view( 'settings/region' );
				$this->render_view( 'settings/wp-notification-email' );
				$this->render_view( 'settings/wp-notification-name' );
				$this->render_view( 'settings/reply-to' );
				$this->render_view( 'settings/return-path' );
				$this->render_view( 'settings/delete-logs' );

				if ( ! $this->is_pro() ) {
					$this->render_view( 'modals/tracking-prompt' );
					$this->render_view( 'modals/health-report-prompt' );
				}

			?>

			<tr>
				<td colspan="2">
					<p>
						<button type="submit" class="button button-primary"><?php _e( 'Save Changes', 'wp-offload-ses' ); ?></button>
						<a class="wposes-launch-setup-wizard" href="<?php echo $this->get_plugin_page_url( array( 'setup-wizard' => true ), 'self' ); ?>"><?php _e( 'Launch Setup Wizard', 'wp-offload-ses' ); ?></a>
					</p>
				</td>
			</tr>
		</table>
	</form>
</div>
