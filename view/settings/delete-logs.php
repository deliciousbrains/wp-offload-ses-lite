<?php 
$args     = $this->settings->get_setting_args( 'log-duration' );
$disabled = ( isset( $args['disabled'] ) && $args['disabled'] ) ? ' disabled' : '';
?>

<tr class="<?php echo $args['tr_class'];?>">
	<td>
		<h4><?php _e( 'Delete Logs', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>
		<div class="wposes-field-wrap">
			<select name="log-duration" <?php echo $disabled; ?>>
				<?php
					$log_durations = DeliciousBrains\WP_Offload_SES\Email_Log::get_log_durations();

					foreach ( $log_durations as $key => $value ) {
						$selected = '';

						if ( $key === (int) $this->get_email_log()->get_log_duration() ) {
							$selected = 'selected';
						}

						echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_attr( $value ) . '</option>';
					}
				?>
			</select>
		</div>
		<p>
			<?php _e( 'Logs are stored to track email open and click counts.', 'wp-offload-ses' ); ?>
			<a class="general-helper" href="#"></a>
			<span class="helper-message bottom">
				<?php _e( 'Logs are automatically stored in this server\'s database to help you keep track of what your site sends through Amazon SES. Select how long you want to keep logs on the server.', 'wp-offload-ses' ); ?>
			</span>
		</p>
	</td>
</tr>
