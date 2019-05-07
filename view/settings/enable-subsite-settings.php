<?php $args = $this->settings->get_setting_args( 'enable-subsite-settings' ); ?>
<tr class="<?php echo $args['tr_class'];?>">
	<td>
		<?php $this->render_view( 'elements/checkbox', $args ); ?>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>
		<h4><?php _e( 'Enable Subsite Settings', 'wp-offload-ses' ); ?></h4>
		<p>
			<?php _e( 'Let subsites configure their own settings.', 'wp-offload-ses' ); ?>
			<a class="general-helper" href="#"></a>
				<span class="helper-message bottom">
					<?php _e( 'When enabled, subsites will be able to configure their own settings. If no settings are configured for a subsite, the subsite will use the network-level settings configured on this page.', 'wp-offload-ses' ); ?>
				</span>
			</a>
		</p>
	</td>
</tr>
