<td colspan="2" style="padding: 10px 0">
	<table width="100%" style="border: 1px solid #ccd0d4; border-collapse: collapse;">
		<tr style="background-color: #f9f9f9;">
			<th style="padding: 8px 10px;" align="left"><?php _e( 'Subject', 'wp-offload-ses' ); ?></th>
			<th style="width:85px; padding: 8px 10px;"><?php _e( 'Emails Sent', 'wp-offload-ses' ); ?></th>
			<th style="width:85px; padding: 8px 10px;"><?php _e( 'Open Count', 'wp-offload-ses' ); ?></th>
			<th style="width:85px; padding: 8px 10px;"><?php _e( 'Click Count', 'wp-offload-ses' ); ?></th>
		</tr>
		<?php
		foreach ( $sent_emails as $key => $email ) {
			$style = '';
			if ( (int) $key % 2 ) {
				$style = 'background-color: #f9f9f9;';
			}

			$open_count  = $email['open_count'];
			$click_count = $email['click_count'];

			if ( $this->is_pro() ) {
				$open_count  = $open_count ? number_format_i18n( $open_count ) : 0;
				$click_count = $click_count ? number_format_i18n( $click_count ) : 0;
			}
			?>
			<tr style="<?php echo $style; ?>">
				<td style="padding: 8px 10px;"><a href="#" style="text-decoration: none; color: black; cursor: default;"><?php echo esc_html( $email['subject'] ); ?></a></td>
				<td style="padding: 8px 10px;" align="center"><?php echo number_format_i18n( $email['emails_sent'] ); ?></td>
				<td style="padding: 8px 10px;" align="center"><?php echo $open_count; ?></td>
				<td style="padding: 8px 10px;" align="center"><?php echo $click_count; ?></td>
			</tr>
			<?php
		}
		?>
	</table>
</td>