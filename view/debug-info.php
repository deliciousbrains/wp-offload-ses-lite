<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="debug support-section">
	<h3><?php esc_html_e( 'Diagnostic Info', 'wp-offload-ses' ); ?></h3>
	<textarea class="debug-log-textarea" autocomplete="off" readonly></textarea>
	<?php
	$args = array(
		'nonce'               => wp_create_nonce( 'wposes-download-log' ),
		'wposes-download-log' => '1',
		'hash'                => 'support',
	);
	$url = $this->get_plugin_page_url( $args, 'self', false );
	?>
	<a href="<?php echo esc_url( $url ); ?>" class="button"><?php echo esc_html_x( 'Download', 'Download to your computer', 'wp-offload-ses' ); ?></a>
</div>