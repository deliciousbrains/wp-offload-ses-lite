<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wposes-delete-email-prompt" style="display:none;">
	<form id="wposes-delete-email-form">
		<h3>
			<span class="wposes-delete-single"><?php esc_html_e( 'Delete Email', 'wp-offload-ses' ); ?></span>
			<span class="wposes-delete-multiple"><?php esc_html_e( 'Delete Emails', 'wp-offload-ses' ); ?></span>
		</h3>
		<p class="wposes-delete-single"><?php echo wp_kses( sprintf( __( 'Are you sure you want to delete the email %s?', 'wp-offload-ses' ), '<span id="wposes-delete-email-subject"></span>' ), array( 'span' => array( 'id' => array() ) ) ); ?></p>
		<p class="wposes-delete-multiple"><?php esc_html_e( 'Are you sure you want to delete the selected emails?', 'wp-offload-ses' ); ?></p>
		<p class="actions select">
			<span>
				<a href="#" class="wposes-modal-cancel"><?php esc_html_e( 'Cancel', 'wp-offload-ses' ); ?></a>
			</span>
			<button id="wposes-delete-email-btn" class="button button-primary">
				<span class="wposes-delete-single"><?php esc_html_e( 'Delete Email', 'wp-offload-ses' ); ?></span>
				<span class="wposes-delete-multiple"><?php esc_html_e( 'Delete Emails', 'wp-offload-ses' ); ?></span>				
			</button>
		</p>
	</form>
</div>
