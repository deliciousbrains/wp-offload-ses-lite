<div class="wposes-tracking-prompt" style="display: none;">

	<h3><?php _e( 'Upgrade to View Reports', 'wp-offload-ses' ); ?></h3>

	<p>
		<?php
		printf(
			__( 'Enabling tracking in the Lite version of this plugin will save statistics to the database, but you must <a href="%s" target="_blank">upgrade</a> to view those statistics in the Reports tab.', 'wp-offload-ses' ),
			$this->dbrains_url( '/wp-offload-ses/' )
		);
		?>
	</p>

	<button class="button button-primary wposes-modal-cancel"><?php _e( 'OK', 'wp-offload-ses' ); ?></button>

</div>
