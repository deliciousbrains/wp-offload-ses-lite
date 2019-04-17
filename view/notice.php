<?php
$type          = ( isset( $type ) ) ? $type : 'notice-info';
$dismissible   = ( isset( $dismissible ) ) ? $dismissible : false;
$inline        = ( isset( $inline ) ) ? $inline : false;
$id            = ( isset( $id ) ) ? 'id="' . $id . '"' : '';
$style         = ( isset( $style ) ) ? $style : '';
$auto_p        = ( isset( $auto_p ) ) ? $auto_p : 'true';
$class         = ( isset( $class ) ) ? $class : '';
$show_callback = ( isset( $show_callback ) && false !== $show_callback ) ? array( $GLOBALS[ $show_callback[0] ], $show_callback[1] ) : false;
$callback_args = ( isset( $callback_args ) ) ? $callback_args : array();
?>
<div <?php echo $id; ?> class="notice <?php echo $type; ?><?php echo ( $dismissible ) ? ' is-dismissible' : ''; ?> wposes-notice <?php echo ( $inline ) ? ' inline' : ''; ?> <?php echo ( '' !== $class ) ? ' ' . $class : ''; ?>" style="<?php echo $style; ?>">
<?php if ( $auto_p ) : ?>
	<p>
<?php endif; ?>
		<?php echo $message; // xss ok ?>
		<?php if ( false !== $show_callback && is_callable( $show_callback ) ) : ?>
			<a href="#" class="wposes-notice-toggle" data-hide="<?php _e( 'Hide', 'wp-offload-ses' ); ?>"><?php _e( 'Show', 'wp-offload-ses' ); ?></a>
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
