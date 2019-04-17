<div class="wposes-verify-sender-prompt" style="display:none;">

	<form id="wposes-verified-sender-form" class="wposes-add-sender">

		<h3 id="wposes-add-sender-title"><?php _e( 'Add Verified Sender', 'wp-offload-ses' ); ?></h3>
		<h3 id="wposes-delete-sender-title"><?php _e( 'Delete Sender', 'wp-offload-ses' ); ?></h3>

		<div id="wposes-enter-sender">
			<p>
				<input id="wposes-domain" type="radio" name="sender-type" value="domain" checked="checked" />
				<label for="wposes-domain"><?php _e( 'Verify Domain (recommended)', 'wp-offload-ses' ); ?></label>
				<input id="wposes-email" type="radio" name="sender-type" value="email" />
				<label for="wposes-email"><?php _e( 'Verify Email Address', 'wp-offload-ses' ); ?></label>
			</p>

			<p>
				<span class="wposes-show-domain"><?php _e( 'Domain:', 'wp-offload-ses' ); ?></span>
				<span class="wposes-show-email" style="display: none;"><?php _e( 'Email Address:', 'wp-offload-ses' ); ?></span>
				<?php $invalid_domain = __( 'Please enter a valid domain name (without http:// or https://).', 'wp-offload-ses' ); ?>
				<input id="wposes-verify-domain" class="wposes-show-domain wposes-sender-name" type="text" name="new-sender" pattern="^(?!http:|https:|www.*$).*" title="<?php echo $invalid_domain; ?>" value="" required />
				<input id="wposes-verify-email" class="wposes-show-email wposes-sender-name" type="email" name="new-sender" style="display: none;" required />
			</p>

			<p class="wposes-show-domain"><?php _e( 'Enter a domain above to generate the necessary TXT records for the domain.', 'wp-offload-ses' ); ?></p>
			<p class="wposes-show-email" style="display: none"><?php _e( 'Enter the email address that you want to verify with Amazon SES.', 'wp-offload-ses' ); ?></p>
			
			<?php wp_nonce_field( 'wposes_verify_sender' ); ?>
		</div>

		<div id="wposes-confirm-email">
			<p><?php printf( __( 'A confirmation email has been sent to %s.', 'wp-offload-ses' ), '<span class="wposes-sender"></span>' ); ?></p>
			<p><?php _e( 'Click the link in the email to add this email address as a verified sender.', 'wp-offload-ses' ); ?></p>
		</div>

		<div id="wposes-update-dns">
			<p><?php printf( __( 'A verification request has been sent for %s.', 'wp-offload-ses' ), '<span class="wposes-sender"></span>' ); ?></p>
			<p><?php _e( 'Please update your DNS to add the following TXT record:', 'wp-offload-ses' ); ?></p>

			<table>
				<thead>
					<tr>
						<th><?php _e( 'Name', 'wp-offload-ses' ); ?></th>
						<th><?php _e( 'Value', 'wp-offload-ses' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><code id="wposes-dns-name" data-wposes-copy>_amazonses.domain.com</code></td>
						<td><code id="wposes-dns-value" data-wposes-copy></code></td>
					</tr>
				</tbody>
			</table>

			<p>
				<?php
					printf(
						__( 'For more information on updating the DNS, please see <a href="%s" target="_blank">Amazon SES Domain Verification TXT Records</a>.', 'wp-offload-ses' ),
						'https://docs.aws.amazon.com/ses/latest/DeveloperGuide/dns-txt-records.html'
					);
				?>
			</p>
		</div>

		<div id="wposes-delete-sender">
			<p><?php printf( __( 'Are you sure you want to remove %s from your verified senders? You will no longer be able to use this sender with SES.', 'wp-offload-ses' ), '<span class="wposes-sender"></span>' ); ?></p>
		</div>

		<div class="wposes-verification-errors" style="display: none;"></div>

		<p class="actions select">
			<span>
				<a href="#" class="wposes-modal-cancel"><?php _e( 'Cancel', 'wp-offload-ses' ); ?></a>
			</span>
			<button id="wposes-verify-sender-btn" type="submit" class="button button-primary" ><?php _e( 'Verify Sender', 'wp-offload-ses' ); ?></button>
			<button id="wposes-continue-btn" class="wposes-modal-cancel button button-primary"><?php _e( 'Continue', 'wp-offload-ses' ); ?></button>
			<button id="wposes-delete-sender-btn" class="button button-primary"><?php _e( 'Delete Sender', 'wp-offload-ses' ); ?></button>
		</p>

	</form>

</div>