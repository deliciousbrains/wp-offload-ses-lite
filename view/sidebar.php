<?php
$url_args = array(
	'utm_campaign' => 'WP+Offload+SES+Upgrade',
	'utm_source'   => 'OSES+Lite',
	'utm_medium'   => 'insideplugin',
);
?>
<div class="wposes-sidebar">

	<a class="wposes-banner" href="<?php echo $this->dbrains_url( '/wp-offload-ses/pricing/', $url_args ); ?>"></a>

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
	</div>

	<div class="wposes-discount subscribe block">
		<h2 style="text-align:center; font-weight: normal;"><?php _e( 'Get 20% off your first year of&nbsp;WP Offload SES!', 'wp-offload-ses' ); ?></h2>
		<h3>
			<a href="<?php echo $this->dbrains_url( 'wp-offload-ses/pricing/', $url_args ); ?>" class="wposes-discount-btn">
				<?php _e( 'Get The Discount', 'wp-offload-ses' ); ?>
			</a>
		</h3>
		<p class="wposes-discount-applied">
			<?php _e( '* Discount applied automatically', 'wp-offload-ses' ); ?>
		</p>
		<div class="wposes-testimonial">
			<h3 class="wposes-handle">
				@markdavoli
				<span class="wposes-rating">
					<?php $sidebar_star = $this->plugins_url( 'assets/img/icon-sidebar-star.svg' ); ?>
					<img src="<?php echo $sidebar_star; ?>" alt="*" />
					<img src="<?php echo $sidebar_star; ?>" alt="*" />
					<img src="<?php echo $sidebar_star; ?>" alt="*" />
					<img src="<?php echo $sidebar_star; ?>" alt="*" />
					<img src="<?php echo $sidebar_star; ?>" alt="*" />
				</span>
			</h3>
			<p class="wposes-quote">"Great plugin for WordPress in combination with rock solid Amazon SES for sending email reliably."</p>
		</div>
	</div>

	<div class="credits">
		<h4><?php _e( 'Created & maintained by', 'wp-offload-ses' ); ?></h4>
		<ul>
			<li>
				<a href="<?php echo $this->dbrains_url( '/', $url_args ); ?>">
					<img src="<?php echo $this->plugins_url( 'assets/img/logo-dbi.svg' ); ?>" alt="Delicious Brains Inc." />
					<span>Delicious Brains Inc.</span>
				</a>
			</li>
		</ul>
	</div>

</div>
