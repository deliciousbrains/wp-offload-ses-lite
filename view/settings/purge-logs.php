<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$args            = $this->settings->get_setting_args( 'purge-logs' );
$args['options'] = $this->get_email_status_options();
?>

<tr class="<?php echo esc_attr( $args['tr_class'] ); ?>">
	<td>
		<h4><?php esc_html_e( 'Purge Logs', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo wp_kses_post( $args['setting_msg'] ); ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/select', $args ); ?>
			<button id="purge-now" type="button" class="button button-secondary" disabled="disabled">
				<?php esc_html_e( 'Purge Now', 'wp-offload-ses' ); ?>
			</button>
			<span data-wposes-purge-logs-spinner class="spinner"></span>
		</div>
		<div id="wposes-purge-logs-success" class="notice updated inline" style="display:none;">
			<p><?php esc_html_e( 'Logs purged!', 'wp-offload-ses' ); ?></p>
		</div>
		<div id="wposes-purge-logs-error" class="notice error inline" style="display: none;">
			<p></p>
		</div>
		<p>
			<?php esc_html_e(
				'Permanently delete all selected emails and their open and click tracking data from the logs.',
				'wp-offload-ses'
			); ?>
		</p>
	</td>
</tr>
