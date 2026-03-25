<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wposes-tracking-prompt" style="display: none;">

	<h3><?php esc_html_e( 'Upgrade to View Reports', 'wp-offload-ses' ); ?></h3>

	<p>
		<?php
		echo wp_kses(
			sprintf(
				__( 'Enabling tracking in the Lite version of this plugin will save statistics to the database, but you must <a href="%s" target="_blank">upgrade</a> to view those statistics in the Reports tab.', 'wp-offload-ses' ),
				esc_url( $this->dbrains_url( '/wp-offload-ses/' ) )
			),
			array( 'a' => array( 'href' => array(), 'target' => array() ) )
		);
		?>
	</p>

	<button class="button button-primary wposes-modal-cancel"><?php esc_html_e( 'OK', 'wp-offload-ses' ); ?></button>

</div>
