<?php
use DeliciousBrains\WP_Offload_SES\Utils;
?>
<td colspan="2" style="padding: 10px 0">
	<table width="100%" style="border: 1px solid #ccd0d4; border-collapse: collapse;">
		<tr style="background-color: #f9f9f9;">
			<th align="left" style="padding: 8px 10px;"><?php _e( 'Email', 'wp-offload-ses' ); ?></th>
			<th align="right" style="padding: 8px 10px;"><?php echo $this->is_pro() ? __( 'Actions', 'wp-offload-ses' ) : ''; ?></th>
		</tr>
		<?php
		foreach ( $failed_emails as $key => $email ) {
			$style = '';
			if ( (int) $key % 2 ) {
				$style = 'background-color: #f9f9f9;';
			}
			?>
			<tr style="<?php echo $style; ?>">
				<td style="padding: 8px 10px;">
					<?php
					$link_style = 'text-decoration: none;';
					if ( $this->is_pro() ) {
						$args = array(
							'hash'       => 'activity',
							'view-email' => (int) $email['id'],
						);
						$view_link = $this->get_plugin_page_url( $args );
					} else {
						$view_link   = '#';
						$link_style .= 'cursor: default;';
					}
					?>
					<a href="<?php echo $view_link; ?>" style="<?php echo $link_style; ?>">
						<span style="font-size:11px; color: gray;">
						<?php
						$formatted = Utils::get_date_and_time( $email['date'] );
						echo is_array( $formatted ) ? implode( ' ', $formatted ) : '';
						?>
						</span>
						<br />
						<span style="color: black;"><?php echo esc_html( $email['subject'] ); ?></span>
						<br>
						<span style="font-size: 11px; color: gray;">
						<?php 
						$recipients = maybe_unserialize( $email['recipient'] );
						$recipients = is_array( $recipients ) ? array_filter( $recipients ) : $recipients;

						if ( empty( $recipients ) || ! $recipients ) {
							$recipients = __( '(no recipients)', 'wp-offload-ses' );
						}

						if ( is_array( $recipients ) ) {
							$recipients = implode( ', ', $recipients );
						}

						echo esc_html( str_replace( '@', '&#64;', $recipients ) );
						?>
						</span>
					</a>
				</td>
				<td style="padding: 8px 10px;" align="right" style="text-align: right;"><?php echo isset( $email['actions'] ) ? $email['actions'] : ''; ?></td>
			</tr>
			<?php
		}
		?>
	</table>
</td>