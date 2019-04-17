<div id="tab-sandbox-mode"  data-prefix="wposes" class="wposes-tab wposes-content">
	<h2><?php _e( 'Sandbox Mode', 'wp-offload-ses' ); ?></h2>

	<p><?php _e( 'By default, all new Amazon SES accounts are placed into sandbox mode, which means that you can only send emails to email addresses that are verified with Amazon. To move out of sandbox mode and send emails to any address, you\'ll need to put in a request with Amazon to move to production.', 'wp-offload-ses' ); ?></p>

	<ol>
		<li><?php printf( __( 'Login to your Amazon console and <a href="%s" target="_blank">open an SES Sending Limits Increase case</a>.', 'wp-offload-ses' ), 'https://aws.amazon.com/ses/extendedaccessrequest/' ); ?></li>
		<li>
			<?php _e( 'In the form, make sure you provide the following information:', 'wp-offload-ses' ); ?>
			<ul>
				<li><?php printf( __( 'Region: Set this to %s, the same region that you selected in the previous step.', 'wp-offload-ses' ), '<span class="wposes-region"></span>' ); ?></li>
				<li><?php _e( 'Limit: Select "Desired Daily Sending Quota" and try to estimate the number of emails your site will send per day as the limit value (rounding up is fine).', 'wp-offload-ses' ); ?></li>
				<li><?php _e( 'Mail Type: Select "Transactional" from the drop down list.', 'wp-offload-ses' ); ?></li>
				<li><?php _e( 'My email-sending-complies with the AWS Services Terms and AUP: Yes', 'wp-offload-ses' ); ?></li>
				<li><?php _e( 'I only send to recipients who have specifically requested my mail: Yes', 'wp-offload-ses' ); ?></li>
				<li><?php _e( 'I have a process to handle bounces and complaints: Yes', 'wp-offload-ses' ); ?></li>
				<li><?php _e( 'Use Case Description: Describe the types of emails that your site will be sending.', 'wp-offload-ses' ); ?></li>
			</ul>
		</li>
	</ol>

	<p><?php _e( 'Once you submit the form, Amazon will review your request and send you an email letting you know if it has been approved and your account has been moved out of sandbox mode. Amazon usually reviews your request within one business day.', 'wp-offload-ses' ); ?></p>

	<p><?php _e( 'You can continue setting up this plugin in the meantime by continuing to the next step.', 'wp-offload-ses' ); ?></p>

	<?php
		$args = array(
			'previous_hash' => 'setup-access-keys',
			'next_hash'     => 'verify-sender',
			'next_title'    => __( 'Next: Verify Sender', 'wp-offload-ses' ),
			'step'          => 3,
		);
		$this->render_view( 'setup/nav', $args );
	?>
</div>