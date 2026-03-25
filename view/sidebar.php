<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$url_args = array(
	'utm_campaign' => 'WP+Offload+SES+Upgrade',
);
?>
<div class="wposes-sidebar">

	<a class="wposes-banner" href="<?php echo esc_url( $this->dbrains_url( '/wp-offload-ses/pricing/', $url_args ) ); ?>"></a>

	<div class="wposes-upgrade-details">
		<h1><?php esc_html_e( 'Upgrade', 'wp-offload-ses' ); ?></h1>
		<h3><?php esc_html_e( 'Get a bundle of features with an upgrade to WP Offload SES', 'wp-offload-ses' ); ?></h3>
		<ul>
			<li><?php esc_html_e( 'Email&nbsp;support', 'wp-offload-ses' ); ?></li>
			<li><?php esc_html_e( 'Open and click&nbsp;reporting', 'wp-offload-ses' ); ?></li>
			<li><?php esc_html_e( 'Auto-retry email sending&nbsp;failures', 'wp-offload-ses' ); ?></li>
			<li><?php esc_html_e( 'Manually retry email sending&nbsp;failures', 'wp-offload-ses' ); ?></li>
			<li><?php esc_html_e( 'Manually resend any sent&nbsp;emails', 'wp-offload-ses' ); ?></li>
			<li><?php esc_html_e( 'Search for any&nbsp;email', 'wp-offload-ses' ); ?></li>
			<li><?php esc_html_e( 'View a specific&nbsp;email', 'wp-offload-ses' ); ?></li>
			<li><?php esc_html_e( 'Analyze engagement for a specific&nbsp;email', 'wp-offload-ses' ); ?></li>
		</ul>
	</div>

	<div class="wposes-discount subscribe block">
		<h2 style="text-align:center; font-weight: normal;"><?php esc_html_e( 'Get 20% off your first year of&nbsp;WP Offload SES!', 'wp-offload-ses' ); ?></h2>
		<h3>
			<a href="<?php echo esc_url( $this->dbrains_url( 'wp-offload-ses/pricing/', $url_args ) ); ?>" class="wposes-discount-btn">
				<?php esc_html_e( 'Get The Discount', 'wp-offload-ses' ); ?>
			</a>
		</h3>
		<p class="wposes-discount-applied">
			<?php esc_html_e( '* Discount applied automatically', 'wp-offload-ses' ); ?>
		</p>
		<div class="wposes-testimonial">
			<h3 class="wposes-handle">
				@markdavoli
				<span class="wposes-rating">
					<?php $sidebar_star = $this->plugins_url( 'assets/img/icon-sidebar-star.svg' ); ?>
					<img src="<?php echo esc_url( $sidebar_star ); ?>" alt="*"/>
					<img src="<?php echo esc_url( $sidebar_star ); ?>" alt="*"/>
					<img src="<?php echo esc_url( $sidebar_star ); ?>" alt="*"/>
					<img src="<?php echo esc_url( $sidebar_star ); ?>" alt="*"/>
					<img src="<?php echo esc_url( $sidebar_star ); ?>" alt="*"/>
				</span>
			</h3>
			<p class="wposes-quote">"Great plugin for WordPress in combination with rock solid Amazon SES for sending email reliably."</p>
		</div>
	</div>
</div>
