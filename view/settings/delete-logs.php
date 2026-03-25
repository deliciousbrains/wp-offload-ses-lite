<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$args            = $this->settings->get_setting_args( 'log-duration' );
$args['options'] = DeliciousBrains\WP_Offload_SES\Email_Log::get_log_durations();
$args['value']   = (int) $this->get_email_log()->get_log_duration();
?>

	<tr class="<?php echo esc_attr( $args['tr_class'] ); ?>">
		<td>
			<h4><?php esc_html_e( 'Delete Logs', 'wp-offload-ses' ); ?></h4>
		</td>
		<td>
			<?php echo wp_kses_post( $args['setting_msg'] ); ?>
			<div class="wposes-field-wrap">
				<?php $this->render_view( 'elements/select', $args ); ?>
			</div>
			<p>
				<?php esc_html_e( 'Logs are stored to track email open and click counts.', 'wp-offload-ses' ); ?>
				<a class="general-helper" href="#"></a>
				<span class="helper-message bottom">
					<?php esc_html_e(
						'Logs are automatically stored in this server\'s database to help you keep track of what your site sends through Amazon SES. Select how long you want to keep logs on the server.',
						'wp-offload-ses'
					); ?>
				</span>
			</p>
		</td>
	</tr>

<?php $args = $this->settings->get_setting_args( 'delete-successful' ); ?>
	<tr class="<?php echo esc_attr( $args['tr_class'] ); ?>">
		<td>
			<?php $this->render_view( 'elements/checkbox', $args ); ?>
		</td>
		<td>
			<?php echo wp_kses_post( $args['setting_msg'] ); ?>
			<h4><?php esc_html_e( 'Instantly Remove Successfully Sent Emails From The Log', 'wp-offload-ses' ); ?></h4>
			<p>
				<?php esc_html_e(
					'As soon as an email is successfully sent, delete it from the log. Opens and clicks will not be tracked for emails deleted from the log.',
					'wp-offload-ses'
				); ?>
			</p>
		</td>
	</tr>

<?php
if ( $this->is_pro() ) {
	$args = $this->settings->get_setting_args( 'delete-re-sent-failed' );
	?>
	<tr class="<?php
	echo esc_attr( $args['tr_class'] ); ?>">
		<td>
			<?php $this->render_view( 'elements/checkbox', $args ); ?>
		</td>
		<td>
			<?php echo wp_kses_post( $args['setting_msg'] ); ?>
			<h4><?php esc_html_e( 'Remove Successfully Re-sent Failed Emails From The Log', 'wp-offload-ses' ); ?></h4>
			<p>
				<?php esc_html_e(
					'As soon as a failed email is successfully re-sent, delete it from the log.',
					'wp-offload-ses'
				); ?>
			</p>
		</td>
	</tr>
	<?php
}
?>
