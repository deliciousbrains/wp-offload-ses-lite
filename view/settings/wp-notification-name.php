<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$args          = $this->settings->get_setting_args( 'default-email-name' );
$args['type']  = 'text';
?>

<tr class="<?php echo esc_attr( $args['tr_class'] ); ?>">
	<td>
		<h4><?php esc_html_e( 'WordPress Notification Name', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo wp_kses_post( $args['setting_msg'] ); ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/text-field', $args ); ?>
		</div>
		<p><?php esc_html_e( 'This overrides the From name of "WordPress" that WordPress uses by default for notifications.', 'wp-offload-ses' ); ?></p>
	</td>
</tr>
