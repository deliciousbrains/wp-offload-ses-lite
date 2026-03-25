<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$args         = $this->settings->get_setting_args( 'reply-to' );
$args['type'] = 'email';
?>

<tr class="<?php echo esc_attr( $args['tr_class'] ); ?>">
	<td>
		<h4><?php esc_html_e( 'Reply To', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo wp_kses_post( $args['setting_msg'] ); ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/text-field', $args ); ?>
		</div>
		<p>
			<?php esc_html_e( 'The optional reply-to email address for outgoing mail. Leave blank to get this from the email headers.', 'wp-offload-ses' ); ?>
			<a class="general-helper" href="#"></a>
			<span class="helper-message bottom">
				<?php esc_html_e( 'Leaving this blank will let other plugins set the reply-to header on a per-plugin basis. If you\'d like to override the reply-to for all emails, enter an email address here.', 'wp-offload-ses' ); ?>
			</span>
		</p>
	</td>
</tr>
