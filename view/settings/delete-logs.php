<?php

$args            = $this->settings->get_setting_args( 'log-duration' );
$args['options'] = DeliciousBrains\WP_Offload_SES\Email_Log::get_log_durations();
$args['value']   = (int) $this->get_email_log()->get_log_duration();
?>

<tr class="<?php echo $args['tr_class']; ?>">
	<td>
		<h4><?php _e( 'Delete Logs', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/select', $args ); ?>
		</div>
		<p>
			<?php _e( 'Logs are stored to track email open and click counts.', 'wp-offload-ses' ); ?>
			<a class="general-helper" href="#"></a>
			<span class="helper-message bottom">
				<?php _e(
					'Logs are automatically stored in this server\'s database to help you keep track of what your site sends through Amazon SES. Select how long you want to keep logs on the server.',
					'wp-offload-ses'
				); ?>
			</span>
		</p>
	</td>
</tr>

<?php $args = $this->settings->get_setting_args( 'delete-successful' ); ?>
<tr class="<?php echo $args['tr_class']; ?>">
	<td>
		<?php $this->render_view( 'elements/checkbox', $args ); ?>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>
		<h4><?php _e( 'Instantly Remove Successfully Sent Emails From The Log', 'wp-offload-ses' ); ?></h4>
		<p>
			<?php _e(
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
	echo $args['tr_class']; ?>">
		<td>
			<?php $this->render_view( 'elements/checkbox', $args ); ?>
		</td>
		<td>
			<?php echo $args['setting_msg']; ?>
			<h4><?php _e( 'Remove Successfully Re-sent Failed Emails From The Log', 'wp-offload-ses' ); ?></h4>
			<p>
				<?php _e(
					'As soon as a failed email is successfully re-sent, delete it from the log.',
					'wp-offload-ses'
				); ?>
			</p>
		</td>
	</tr>
	<?php
}

$args            = $this->settings->get_setting_args( 'purge-logs' );
$args['options'] = $this->get_email_status_options();
?>

<tr class="<?php echo $args['tr_class']; ?>">
	<td>
		<h4><?php _e( 'Purge Logs', 'wp-offload-ses' ); ?></h4>
	</td>
	<td>
		<?php echo $args['setting_msg']; ?>
		<div class="wposes-field-wrap">
			<?php $this->render_view( 'elements/select', $args ); ?>
			<button id="purge-now" type="button" class="button button-secondary" disabled="disabled">
				<?php _e( 'Purge Now', 'wp-offload-ses' ); ?>
			</button>
			<span data-wposes-purge-logs-spinner class="spinner"></span>
		</div>
		<div id="wposes-purge-logs-success" class="notice updated inline" style="display:none;">
			<p><?php _e( 'Logs purged!', 'wp-offload-ses' ); ?></p>
		</div>
		<div id="wposes-purge-logs-error" class="notice error inline" style="display: none;">
			<p></p>
		</div>
		<p>
			<?php _e(
				'Permanently delete all selected emails and their open and click tracking data from the logs.',
				'wp-offload-ses'
			); ?>
		</p>
	</td>
</tr>
