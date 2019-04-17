<div class="support support-section">
	<p><?php _e( 'As this is a free plugin, we do not provide support.', 'wp-offload-ses'); ?></p>

	<p><?php printf( __( 'You may ask the WordPress community for help by posting to the <a href="%s">WordPress.org support forum</a>. Response time can range from a few days to a few weeks and will likely be from a non-developer.', 'wp-offload-ses'), 'https://wordpress.org/plugins/wp-ses/' ); ?></p>

	<?php $url = $this->dbrains_url( '/wp-offload-ses/', array(
		'utm_campaign' => 'WP+Offload+SES',
		'utm_content'  => 'support+tab',
	) ); ?>
	<p class="upgrade-to-pro"><?php printf( __( 'If you want a <strong>timely response via email from a developer</strong> who works on this plugin, <a href="%s">upgrade</a> and send us an email.', 'wp-offload-ses' ), $url ); ?></p>

	<p><?php printf( __( 'If you\'ve found a bug, please <a href="%s">submit an issue on GitHub</a>.', 'wp-offload-ses' ), 'https://github.com/deliciousbrains/wp-offload-ses-lite/issues' ); ?></p>

</div>
