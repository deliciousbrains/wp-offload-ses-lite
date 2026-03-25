<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$args = $this->settings->get_setting_args( 'enable-subsite-settings' );
?>
<tr class="<?php echo esc_attr( $args['tr_class'] ); ?>">
	<td>
		<?php $this->render_view( 'elements/checkbox', $args ); ?>
	</td>
	<td>
		<?php echo wp_kses_post( $args['setting_msg'] ); ?>
		<h4><?php esc_html_e( 'Enable Subsite Settings', 'wp-offload-ses' ); ?></h4>
		<p>
			<?php esc_html_e( 'Let subsites configure their own settings.', 'wp-offload-ses' ); ?>
			<a class="general-helper" href="#"></a>
				<span class="helper-message bottom">
					<?php esc_html_e( 'When enabled, subsites will be able to configure their own settings. If no settings are configured for a subsite, the subsite will use the network-level settings configured on this page.', 'wp-offload-ses' ); ?>
				</span>
			</a>
		</p>
	</td>
</tr>
