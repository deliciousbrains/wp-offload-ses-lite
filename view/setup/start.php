<div id="tab-start" data-prefix="wposes" class="wposes-tab wposes-content">
	<div>
		<div style="float: left; margin: 0 1em 1em 0">
			<img class="wposes-logo" src="<?php echo $this->plugins_url( 'assets/img/ses-logo.jpg' ) ?>" />
		</div>
		<h2><?php _e( 'Thanks for installing WP Offload SES! 🎉', 'wp-offload-ses' ); ?></h2>
		<p><?php _e( 'This setup wizard will guide you through the initial setup of the plugin. Once complete, your site will start sending emails through Amazon SES.', 'wp-offload-ses' ); ?></p>
		<p><?php printf( __( 'If you\'d rather configure everything manually, you can <a href="%s">skip setup</a> and enter the settings for the plugin.', 'wp-offload-ses' ), $this->get_plugin_page_url( array( 'skip-setup' => true, 'hash' => 'settings' ), 'self' ) ); ?></p>
		<div class="clearfix"></div>
	</div>

	<h2><?php _e( 'Amazon SES Setup', 'wp-offload-ses' ); ?></h2>
	<p>
		<?php _e( 'The following steps will walk you through the process of setting up the WP Offload SES plugin. Each step is clearly outlined with screenshots to guide you through these processes:', 'wp-offload-ses' ); ?>
	</p>
	<ul>
		<li><?php _e( 'Getting your AWS Access Keys and using them with this plugin', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Moving your Amazon SES account out of sandbox mode', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Validating a sender or domain to use Amazon SES', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Configuring the plugin to send emails over Amazon SES', 'wp-offload-ses' ); ?></li>
	</ul>
	<p>
		<?php _e( 'The whole process usually takes 15-30 minutes, depending on your pace and external factors like DNS propagation and Amazons\' account verification.', 'wp-offload-ses' ); ?>
	</p>

	<div class="notice notice-info inline wposes-click-to-copy-show" style="display: none;">
		<p>
			<?php _e( 'Text that looks like <code data-wposes-copy>this</code> can be clicked to copy it to your clipboard for easy pasting. <code data-wposes-copy>Try me!</code>', 'wp-offload-ses' ); ?>
		</p>
	</div>
	<p>
		<?php
			$msg = __( 'When you\'re ready to begin, click <strong>Get Started</strong> below.', 'wp-offload-ses' );

			if ( $this->is_pro() && ( ! is_multisite() || is_network_admin() ) ) {
				$msg = __( 'When you\'re ready to begin, enter your license key and click <strong>Get Started</strong> below.', 'wp-offload-ses' );
			}

			echo $msg;
		?>
	</p>

	<?php
		if ( ! is_multisite() || is_network_admin() ) {
			do_action( 'wposes_licence_field' );
		}
	?>

	<?php
		$args = array(
			'previous_hash' => '',
			'next_hash'     => 'create-iam-user',
			'next_title'    => __( 'Get Started', 'wp-offload-ses' ),
			'step'          => 0,
		);
		$this->render_view( 'setup/nav', $args );
	?>
</div>