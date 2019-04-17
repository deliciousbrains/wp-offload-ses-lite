<?php
$args          = $this->settings->get_setting_args( 'default-email-name' );
$args['type']  = 'text';
?>

<tr class="<?php echo $args['tr_class'];?>">
	<td>
		<h4><?php _e( 'WordPress Notification Name', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/text-field', $args ); ?>
		</div>
		<p><?php _e( 'This overrides the From name of "WordPress" that WordPress uses by default for notifications.', 'wp-offload-ses' ); ?></p>
	</td>
</tr>
