<?php
$args = $this->settings->get_setting_args( 'region' );
$disabled = ( isset( $args['disabled'] ) && $args['disabled'] ) ? ' disabled' : '';
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
			<select name="region" <?php echo $disabled; ?>>
				<?php
					$regions = DeliciousBrains\WP_Offload_SES\SES_API::get_regions();

					foreach ( $regions as $region_key => $region_name ) {
						$selected = '';

						if ( $region_key === $this->settings->get_setting( 'region' ) ) {
							$selected = 'selected';
						}

						echo '<option value="' . esc_attr( $region_key ) . '" ' . $selected . '>' . esc_attr( $region_name ) . '</option>';
					}
				?>
			</select>
		</div>
	</td>
</tr>
