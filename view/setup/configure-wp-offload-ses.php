<?php 
	use DeliciousBrains\WP_Offload_SES\Utils;
?>

<div id="tab-configure-wp-offload-ses"  data-prefix="wposes" class="wposes-tab wposes-content">
	<h2><?php _e( 'Configure WP Offload SES', 'wp-offload-ses' ); ?></h2>
	<p><?php _e( 'Configure the settings below to finish setting up the plugin.', 'wp-offload-ses' ); ?></p>

	<form id="wposes-setup-final-settings" method="post">
		<input type="hidden" name="completed-setup" value="1" />
		<table class="form-table">
			<?php
				$this->render_view( 'settings/wp-notification-email' );
				$this->render_view( 'settings/wp-notification-name' );
				$this->render_view( 'settings/return-path' );
				$this->render_view( 'settings/delete-logs' );
			?>
		</table>
	</form>

	<?php
		$args = array(
			'previous_hash' => 'complete-verification',
			'next_hash'     => 'settings',
			'next_title'    => __( 'Save and Complete Setup', 'wp-offload-ses' ),
			'step'          => 6,
		);
		$this->render_view( 'setup/nav', $args );
	?>
</div>
