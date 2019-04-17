<?php
use DeliciousBrains\WP_Offload_SES\Pro\Reports_List_Table;
use DeliciousBrains\WP_Offload_SES\Utils;

$hide_stats = Utils::get_first_defined_constant( array( 'WP_SES_HIDE_STATS', 'WPOSES_HIDE_STATS' ) );

if ( $hide_stats && constant( $hide_stats ) ) {
	$hide_stats = true;
} else {
	$hide_stats = false;
}
?>

<div id="tab-reports" data-prefix="wposes" class="wposes-tab wposes-content">
	<?php if ( $hide_stats ): ?>
		<!-- Stats hidden via constant -->
	<?php elseif ( $this->get_aws()->needs_access_keys() ): ?>
		<div class="wposes-need-help wposes-error inline">
			<span class="dashicons dashicons-info"></span>
			<?php printf( __( '<a href="%s">Define your AWS keys</a> to view reports and send statistics.', 'wp-offload-ses' ), '#settings' ) ?>
		</div>
	<?php else: ?>
		<?php
		$api   = $this->get_ses_api();
		$quota = $api->get_send_quota();

		if ( ! is_wp_error( $quota ) ) :
		?>
		<div class="wposes-notice inline subtle wposes-quota">
			<dl>
				<dt><?php _e( 'Sending Quota:', 'wp-offload-ses' ); ?></dt>
				<dd><?php printf( _n( '%s email per 24 hour period', '%s emails per 24 hour period', $quota['limit'], 'wp-offload-ses' ), $quota['limit'] ) ?></dd>
				<dt><?php _e( 'Max Send Rate:', 'wp-offload-ses' ); ?></dt>
				<dd><?php printf( _n( '%s email per second', '%s emails per second', $quota['rate'], 'wp-offload-ses' ), $quota['rate'] ) ?></dd>
				<dt><?php _e( 'Total Sent:', 'wp-offload-ses' ); ?></dt>
				<dd><?php printf( _n( '%s email sent during the previous 24 hours', '%s emails sent during the previous 24 hours', $quota['sent'], 'wp-offload-ses' ), $quota['sent'] ) ?></dd>
				<dt><?php _e( 'Quota Used:', 'wp-offload-ses' ); ?></dt>
				<dd><?php printf( __( '%d%%', 'wp-offload-ses' ), $quota['used'] ) ?></dd>
			</dl>
			<p><a href="https://aws.amazon.com/ses/extendedaccessrequest/" target="_blank"><?php _e( 'Increase Sending Limits', 'wp-offload-ses' ); ?></a></p>
		</div>
		<?php else: ?>
		<div class="notice error inline wposes-notice">
			<p><?php echo $quota->get_error_message(); ?></p>
		</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	if ( $this->is_pro() ) {
		if ( $this->is_valid_licence() ) {
			$table = new Reports_List_Table();
			$table->prepare_items();
			$table->display();
		} else {
			?>
			<div id="wposes-reports-bg">
				<div id="wposes-reports-prompt">
					<h2><?php _e( 'Activate Your License', 'wp-offload-ses' ); ?></h2>
					<p><?php _e( 'To view reports, queue emails, and gain access to email support.', 'wp-offload-ses' ); ?></p>
					<a class="button button-primary" href="#licence"><?php _e( 'Enter License Key', 'wp-offload-ses' ); ?></a>
				</div>
			</div>
			<?php
		}
	} else {
		?>
		<div id="wposes-reports-bg">
			<div id="wposes-reports-prompt">
				<h2><?php _e( 'Upgrade to WP Offload SES', 'wp-offload-ses' ); ?></h2>
				<p><?php _e( 'And get access to detailed reports, click and open tracking, and more.', 'wp-offload-ses' ); ?></p>
				<a class="button button-primary" href="<?php echo $this->dbrains_url( '/wp-offload-ses/' ); ?>"><?php _e( 'Upgrade Now', 'wp-offload-ses' ); ?></a>
			</div>
		</div>
		<?php
	}
	?>
</div>
