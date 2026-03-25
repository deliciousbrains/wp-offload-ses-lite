<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$args          = $this->settings->get_setting_args( 'default-email' );
$args['type']  = 'email';
$args['value'] = DeliciousBrains\WP_Offload_SES\Utils::get_wordpress_default_email();
?>

<tr class="<?php echo esc_attr( $args['tr_class'] ); ?>">
	<td>
		<h4><?php esc_html_e( 'WordPress Notification Email', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo wp_kses_post( $args['setting_msg'] ); ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/text-field', $args ); ?>
		</div>
		<p>
			<?php
				echo esc_html( sprintf(
					__( 'This overrides the %s email address that WordPress uses as the From for notifications.', 'wp-offload-ses' ),
					DeliciousBrains\WP_Offload_SES\Utils::get_wordpress_default_email( false )
				) );
			?>
			<a class="general-helper" href="#"></a>
			<span class="helper-message bottom">
				<?php echo esc_html( sprintf(
					__( 'By default, WordPress sends notifications from %s. If you don\'t have that email setup to send through Amazon SES, enter an email address here to be used instead.', 'wp-offload-ses' ),
					DeliciousBrains\WP_Offload_SES\Utils::get_wordpress_default_email( false )
				) ); ?>
			</span>
		</p>
	</td>
</tr>