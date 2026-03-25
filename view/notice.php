<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$type          = ( isset( $type ) ) ? $type : 'notice-info';
$dismissible   = ( isset( $dismissible ) ) ? $dismissible : false;
$inline        = ( isset( $inline ) ) ? $inline : false;
$id            = ( isset( $id ) ) ? 'id="' . esc_attr( $id ) . '"' : '';
$style         = ( isset( $style ) ) ? $style : '';
$auto_p        = ( isset( $auto_p ) ) ? $auto_p : 'true';
$class         = ( isset( $class ) ) ? $class : '';
$show_callback = ( isset( $show_callback ) && false !== $show_callback ) ? array( $GLOBALS[ $show_callback[0] ], $show_callback[1] ) : false;
$callback_args = ( isset( $callback_args ) ) ? $callback_args : array();
?>
<div <?php echo wp_kses_post( $id ); ?> class="notice <?php echo esc_attr( $type ); ?><?php echo ( $dismissible ) ? ' is-dismissible' : ''; ?> wposes-notice <?php echo ( $inline ) ? ' inline' : ''; ?> <?php echo ( '' !== $class ) ? ' ' . esc_attr( $class ) : ''; ?>" style="<?php echo esc_attr( $style ); ?>">
<?php if ( $auto_p ) : ?>
	<p>
<?php endif; ?>
		<?php echo wp_kses_post( $message ); ?>
		<?php if ( false !== $show_callback && is_callable( $show_callback ) ) : ?>
			<a href="#" class="wposes-notice-toggle" data-hide="<?php esc_html_e( 'Hide', 'wp-offload-ses' ); ?>"><?php esc_html_e( 'Show', 'wp-offload-ses' ); ?></a>
		<?php endif; ?>
<?php if ( $auto_p ) : ?>
	</p>
<?php endif; ?>
<?php if ( false !== $show_callback && is_callable( $show_callback ) ) : ?>
	<div class="wposes-notice-toggle-content" style="display: none;">
		<?php call_user_func_array( $show_callback, $callback_args ); ?>
	</div>
<?php endif; ?>
</div>
