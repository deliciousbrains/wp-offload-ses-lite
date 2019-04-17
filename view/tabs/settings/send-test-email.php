<div id="tab-send-test-email" data-prefix="wposes" class="wposes-tab wposes-content">

	<h3><?php _e( 'Send Test Email', 'wp-offload-ses' ); ?></h3>

	<form id="wposes-send-test-email-form">
		<p><?php _e( 'Please note: If your account is still in sandbox mode, it must be sent to one of the verified senders or the owner of the SES account. Emails sent via this form will not be logged or show up in reports.', 'wp-offload-ses' ); ?></p>

		<h4><?php _e( 'Email Address', 'wp-offload-ses' ); ?></h4>

		<p><input id="wposes-test-email-address" name="test-email-address" type="email" required /></p>

		<div id="wposes-test-email-sent" class="notice updated inline" style="display:none;">
			<p><?php _e( 'Message sent!', 'wp-offload-ses' ); ?></p>
		</div>
		<div id="wposes-test-email-error" class="notice error inline" style="display: none;">
			<p></p>
		</div>

		<button id="wposes-send-test-email-btn" type="submit" class="button button-primary"><?php _e( 'Send Test Email', 'wp-offload-ses' ); ?></button>
		<span data-wposes-send-test-email-spinner class="spinner"></span>
	</form>
</div>
