<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$args         = $this->settings->get_setting_args( 'return-path' );
$args['type'] = 'email';
?>

<tr class="<?php echo esc_attr( $args['tr_class'] ); ?>">
	<td>
		<h4><?php esc_html_e( 'Return Path', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo wp_kses_post( $args['setting_msg'] ); ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/text-field', $args ); ?>
		</div>
		<p>
			<?php esc_html_e( 'Bounces and email complaints are sent to this address.', 'wp-offload-ses' ); ?>
			<a class="general-helper" href="#"></a>
			<span class="helper-message bottom">
				<?php esc_html_e( 'Amazon requires that you have a process for handling email bounces and complaints. Entering a verified email here to receive notifications about these events fulfills that requirement.', 'wp-offload-ses' ); ?>
			</span>
		</p>
	</td>
</tr>