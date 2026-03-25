<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wposes-wizard-controls">
	<div class="wposes-wizard-control-row wposes-wizard-control-row-primary">
		<a class="button-primary wposes-wizard-next-btn" href="#<?php echo esc_attr( $next_hash ); ?>"><?php echo esc_html( $next_title ); ?></a>
		<?php if ( 0 !== $step ): ?>
			<a class="wposes-wizard-back" href="#<?php echo esc_attr( $previous_hash ); ?>"><?php esc_html_e( 'Back to Previous Step', 'wp-offload-ses' ); ?></a>
		<?php endif; ?>
	</div>
	<div class="wposes-wizard-control-row wposes-wizard-control-row-secondary">
		<?php if ( 0 !== $step  ): ?>
			<div class="wposes-wizard-step wp-ui-text-icon"><?php echo esc_html( sprintf( __( 'Step %s of %s', 'wp-offload-ses' ), $step, '6' ) ); ?></div>
		<?php endif; ?>

		<a class="wposes-skip-to-settings" href="<?php echo esc_url( $this->get_plugin_page_url( array( 'skip-setup' => true, 'hash' => 'settings' ), 'self' ) ); ?>"><?php esc_html_e( 'Skip to Settings', 'wp-offload-ses' ); ?></a>
	</div>
</div>