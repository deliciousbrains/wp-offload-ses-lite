<?php
$args            = $this->settings->get_setting_args( 'region' );
$args['options'] = DeliciousBrains\WP_Offload_SES\SES_API::get_regions();
$disabled        = ( isset( $args['disabled'] ) && $args['disabled'] ) ? ' disabled' : '';
?>
<tr class="<?php echo $args['tr_class']; ?>">
	<td>
		<h4><?php _e( 'Region', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>		
		<p <?php echo $this->is_plugin_setup() ? 'style="display: none; "' : ''; ?>>
			<?php _e( 'Select a region that you want to use with Amazon SES. For performance purposes, it\'s best to pick the region closest to the server this site is running on.', 'wp-offload-ses' ); ?>
		</p>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/select', $args ); ?>
		</div>
	</td>
</tr>
