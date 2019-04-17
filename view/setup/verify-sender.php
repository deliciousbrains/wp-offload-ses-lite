<div id="tab-verify-sender"  data-prefix="wposes" class="wposes-tab wposes-content">
	<h2><?php _e( 'Verify Email Sender', 'wp-offload-ses' ); ?></h2>

	<div class="wposes-verification-errors updated error" style="display: none;">
		<p><?php _e( 'There was an error verifying the provided sender. Please make sure that your access keys are correct and try again.', 'wp-offload-ses' ); ?><?php echo ' ' . $this->more_info_link( 'wp-offload-ses/doc/sender-verification-errors' ); ?></p>
	</div>

	<p><?php _e( 'Now you\'ll need to verify a sender that you want to send mail through Amazon SES. A sender is simply any email address you\'ll be using in the "From" address of outbound emails.', 'wp-offload-ses' ); ?></p>
	<p><?php _e( 'You can verify individual email addresses, but if you have multiple email addresses on the same domain that will be sending mail, we recommend verifying a domain.', 'wp-offload-ses' ); ?></p>
	<p><?php _e( 'How would you like to verify your sender? You can always add more senders later.', 'wp-offload-ses' ); ?></p>

	<form id="wposes-setup-verify-senders-form">
		<p class="wposes-sender-type-toggle">
			<input id="wposes-domain" type="radio" name="sender-type" value="domain" checked="checked" />
			<label for="wposes-domain"><?php _e( 'Verify Domain (recommended)', 'wp-offload-ses' ); ?></label>
			<input id="wposes-email" type="radio" name="sender-type" value="email" />
			<label for="wposes-email"><?php _e( 'Verify Email Address', 'wp-offload-ses' ); ?></label>
		</p>

		<p class="wposes-show-domain"><strong><?php _e( 'What domain would you like to verify?', 'wp-offload-ses' ); ?></strong></p>
		<p class="wposes-show-email" style="display: none;"><strong><?php _e( 'What email address would you like to verify?', 'wp-offload-ses' ); ?></strong></p>

		<?php $invalid_domain = __( 'Please enter a valid domain name (without http:// or https://).', 'wp-offload-ses' ); ?>
		<input id="wposes-setup-verify-domain" class="wposes-show-domain wposes-setup-verify-input" type="text" value="<?php echo DeliciousBrains\WP_Offload_SES\Utils::current_domain(); ?>" pattern="^(?!http:|https:|www.*$).*" title="<?php echo $invalid_domain; ?>" required="required" />
		<input id="wposes-setup-verify-email" class="wposes-show-email wposes-setup-verify-input" type="email" style="display:none;" value="<?php echo get_option( 'admin_email' ); ?>" required="required" />
	</form>

	<?php
		$args = array(
			'previous_hash' => 'sandbox-mode',
			'next_hash'     => 'complete-verification',
			'next_title'    => __( 'Next: Complete Verification', 'wp-offload-ses' ),
			'step'          => 4,
		);
		$this->render_view( 'setup/nav', $args );
	?>
</div>