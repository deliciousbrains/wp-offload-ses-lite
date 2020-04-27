<table>
	<!-- upgrade stuff (lite) -->
	<tr>
		<td>
			<h3 style="font-size: 18px; font-weight: normal; margin: 10px 0 0 0;"><?php _e( 'Want a better handle on your email failures?', 'wp-offload-ses' ); ?></h3>
			<p style="margin: 4px 0 20px 0;"><?php _e( 'Upgrade to WP Offload SES and get these awesome email failure handling features', 'wp-offload-ses' ); ?></p>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td width="285" bgcolor="#f2f2f2" style="background-color: #f2f2f2; padding: 15px; width: 285px" valign="top">
						<h4 style="font-size: 16px; font-weight: normal; margin: 0;"><?php _e( 'Auto-Retry Email Sending Failures', 'wp-offload-ses' ); ?></h4>
						<p style="font-size: 13px; margin-bottom: 0;"><?php _e( 'Every Amazon SES account has a max send rate. If you try to send more emails per second than your account rate, Amazon SES will return an error and refuse to send the email which could result in dropped emails if not handled properly. WP Offload SES is aware of your SES account\'s send rate and will stay within the limit, but in the event of a failed send (e.g. a networking issue) the robust queue system will retry sending those emails and keep track of failures.', 'wp-offload-ses' ); ?></p>
					</td>
					<td width="30" style="width: 30px;"></td>
					<td width="285" bgcolor="#f2f2f2" style="background-color: #f2f2f2; padding: 15px; width: 285px;" valign="top">
						<h4 style="font-size: 16px; font-weight: normal; margin: 0;"><?php _e( 'Manually Retry Email Sending Failures', 'wp-offload-ses' ); ?></h4>
						<p style="font-size: 13px; margin-bottom: 0;"><?php _e( 'Let\'s say there was a networking issue that prevented your site from connecting to Amazon SES to send your email. WP Offload SES will automatically retry sending a few times before giving up and calling it a failure. If that happens, you can simply retry those failures once connectivity to Amazon SES is restored. With WP Offload SES none of your emails will get dropped into the ether because of a failure.', 'wp-offload-ses' ); ?></p>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<h3 style="font-size: 18px; font-weight: normal; margin: 18px 0;"><?php _e( 'Upgrading also gets you...', 'wp-offload-ses' ); ?></h3>
					</td>
				</tr>
				<tr>
					<td width="285" bgcolor="#f2f2f2" style="background-color: #f2f2f2; padding: 15px; width: 285px;" valign="top">
						<p style="font-size: 16px; margin: 0;">
							✅ <?php _e( 'Email support', 'wp-offload-ses' ); ?><br />
							✅ <?php _e( 'Open &amp; click reporting', 'wp-offload-ses' ); ?><br />
							✅ <?php _e( 'Search for any email', 'wp-offload-ses' ); ?><br />
							✅ <?php _e( 'View a specific email', 'wp-offload-ses' ); ?><br />
							✅ <?php _e( 'Analyze engagement for a specific email', 'wp-offload-ses' ); ?><br />
						</p>
					</td>
					<td width="30" style="width: 30px;"></td>
					<td width="285" bgcolor="#f2f2f2" style="background-color: #f2f2f2; padding: 15px; width: 285px;" valign="top">
						<h4 style="font-size: 16px; font-weight: normal; margin: 0;"><?php _e( 'Get 20% Off!', 'wp-offload-ses' ); ?></h4>
						<p style="font-size: 13px;"><?php _e( 'Click below to get 20% off your first year of WP Offload SES and gain access to pro features.', 'wp-offload-ses' ); ?></p>
						<?php
						$upgrade_url = $this->dbrains_url(
							'/wp-offload-ses/',
							array(
								'utm_campaign' => 'WP+Offload+SES+20+Percent',
								'utm_source'   => 'Email+health+report',
								'utm_medium'   => 'email',
								'utm_content'  => 'upgrade',
							)
						);
						?>
						<p><a href="<?php echo esc_url( $upgrade_url ); ?>" style="border: 2px solid #000000; padding: 10px; font-size: 16px; color: #000000; background: #ffffff; text-decoration: none;"><?php _e( 'Get WP Offload SES Now', 'wp-offload-ses' ); ?></a></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>