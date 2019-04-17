<?php
$args         = $this->settings->get_setting_args( 'return-path' );
$args['type'] = 'email';
?>

<tr class="<?php echo $args['tr_class'];?>">
	<td>
		<h4><?php _e( 'Return Path', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/text-field', $args ); ?>
		</div>
		<p>
			<?php _e( 'Bounces and email complaints are sent to this address.', 'wp-offload-ses' ); ?>
			<a class="general-helper" href="#"></a>
			<span class="helper-message bottom">
				<?php _e( 'Amazon requires that you have a process for handling email bounces and complaints. Entering a verified email here to receive notifications about these events fulfills that requirement.', 'wp-offload-ses' ); ?>
			</span>
		</p>
	</td>
</tr>