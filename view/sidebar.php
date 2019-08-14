<?php
$url_args = array(
	'utm_campaign' => 'WP+Offload+SES+Upgrade',
	'utm_source'   => 'OSES+Free',
	'utm_medium'   => 'insideplugin',
);
?>
<div class="wposes-sidebar">

	<a class="wposes-banner" href="<?php echo $this->dbrains_url( '/wp-offload-ses/upgrade/', $url_args ); ?>"></a>

	<div class="wposes-upgrade-details">
		<h1><?php _e( 'Upgrade', 'wp-offload-ses' ); ?></h1>
		<h3><?php _e( 'Get a bundle of features with an upgrade to WP Offload SES', 'wp-offload-ses' ); ?></h3>
		<ul>
			<li><?php _e( 'Email&nbsp;support', 'wp-offload-ses' ); ?></li>
			<li><?php _e( 'Open and click&nbsp;reporting', 'wp-offload-ses' ); ?></li>
			<li><?php _e( 'Auto-retry email sending&nbsp;failures', 'wp-offload-ses' ); ?></li>
			<li><?php _e( 'Manually retry email sending&nbsp;failures', 'wp-offload-ses' ); ?></li>
			<li><?php _e( 'Manually resend any sent&nbsp;emails', 'wp-offload-ses' ); ?></li>
			<li><?php _e( 'Search for any&nbsp;email', 'wp-offload-ses' ); ?></li>
			<li><?php _e( 'View a specific&nbsp;email', 'wp-offload-ses' ); ?></li>
			<li><?php _e( 'Analyze engagement for a specific&nbsp;email', 'wp-offload-ses' ); ?></li>
		</ul>

		<p style="margin-bottom: 0;">
			<a href="<?php echo $this->dbrains_url( '/wp-offload-ses/upgrade/', $url_args ); ?>"><?php _e( 'Visit deliciousbrains.com →', 'wp-offload-ses' ); ?></a>
		</p>
	</div>

	<form method="post" action="https://deliciousbrains.com/email-subscribe/" target="_blank" class="subscribe block">
		<h2><?php _e( 'Get 40% Off!', 'wp-offload-ses' ); ?></h2>

		<?php $user = wp_get_current_user(); ?>

		<p class="interesting">
			<?php echo wptexturize( __( "We're celebrating the launch of WP Offload SES with 40% off! Submit your name and email and we'll send you a discount for 40% off the upgrade (limited time only)", 'wp-offload-ses' ) ); ?>
		</p>

		<div class="field">
			<input type="email" name="email" value="<?php echo esc_attr( $user->user_email ); ?>" placeholder="<?php _e( 'Your Email', 'wp-offload-ses' ); ?>"/>
		</div>

		<div class="field">
			<input type="text" name="first_name" value="<?php echo esc_attr( trim( $user->first_name ) ); ?>" placeholder="<?php _e( 'First Name', 'wp-offload-ses' ); ?>"/>
		</div>

		<div class="field">
			<input type="text" name="last_name" value="<?php echo esc_attr( trim( $user->last_name ) ); ?>" placeholder="<?php _e( 'Last Name', 'wp-offload-ses' ); ?>"/>
		</div>

		<input type="hidden" name="campaigns[]" value="22" />
		<input type="hidden" name="source" value="14" />

		<div class="field subscribe-button">
			<button type="submit" class="button"><?php _e( 'Send me the coupon', 'wp-offload-ses' ); ?></button>
		</div>

		<p class="promise"><?php _e( 'We promise we will not use your email for anything else and you can unsubscribe with 1-click anytime.', 'wp-offload-ses' ); ?></p>
	</form>

	<div class="block credits">
		<h4><?php _e( 'Created & maintained by', 'wp-offload-ses' ); ?></h4>
		<ul>
			<li>
				<a href="<?php echo $this->dbrains_url( '/', $url_args ); ?>">
					<img src="//www.gravatar.com/avatar/e62fc2e9c8d9fc6edd4fea5339036a91?size=64" alt="" width="32" height="32">
					<span>Delicious Brains Inc.</span>
				</a>
			</li>
		</ul>
	</div>

</div>
