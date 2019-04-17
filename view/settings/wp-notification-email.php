<?php
$args          = $this->settings->get_setting_args( 'default-email' );
$args['type']  = 'email';
$args['value'] = DeliciousBrains\WP_Offload_SES\Utils::get_wordpress_default_email();
?>

<tr class="<?php echo $args['tr_class'];?>">
	<td>
		<h4><?php _e( 'WordPress Notification Email', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/text-field', $args ); ?>
		</div>
		<p>
			<?php
				printf(
					__( 'This overrides the %s email address that WordPress uses as the From for notifications.', 'wp-offload-ses' ),
					DeliciousBrains\WP_Offload_SES\Utils::get_wordpress_default_email( false )
				);
			?>
			<a class="general-helper" href="#"></a>
			<span class="helper-message bottom">
				<?php echo sprintf(
					__( 'By default, WordPress sends notifications from %s. If you don\'t have that email setup to send through Amazon SES, enter an email address here to be used instead.', 'wp-offload-ses' ),
					DeliciousBrains\WP_Offload_SES\Utils::get_wordpress_default_email( false )
				); ?>
			</span>
		</p>
	</td>
</tr>