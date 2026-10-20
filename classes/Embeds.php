<?php
/**
 * Handles inline embeds for WP Offload SES.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Embeds
 *
 * Inline embeds share the attachments file store and link table, so a file
 * that is both attached and embedded is only stored once. Embed rows are
 * told apart by their Content-ID, which plain attachments never have.
 * Only reads are split this way. Deleting an email's links is inherited
 * unchanged and clears its attachments and embeds together.
 *
 * @since 1.8.3
 */
class Embeds extends Attachments {

	/**
	 * Processes an inline embed, storing it in our custom folder
	 * and the database as necessary.
	 *
	 * @param int    $email_id   The ID of the email sending the embed.
	 * @param string $attachment The path to the embed.
	 * @param string $cid        The Content-ID the message refers to the file by.
	 *
	 * @return bool
	 */
	public function handle_attachment( $email_id, $attachment, $cid = '' ) {
		if ( '' === (string) $cid || ! is_string( $attachment ) ) {
			return false;
		}

		return parent::handle_attachment( $email_id, $attachment, (string) $cid );
	}

	/**
	 * Adds the embed to the provided email.
	 *
	 * @param int    $email_id      The ID of the email to add the embed to.
	 * @param int    $attachment_id The ID of the stored file to add.
	 * @param string $filename      The filename to use.
	 * @param string $cid           The Content-ID the message refers to the file by.
	 *
	 * @return bool
	 */
	public function add_attachment_to_email( $email_id, $attachment_id, $filename, $cid = '' ) {
		// Without a Content-ID the row would read back as a plain attachment.
		if ( '' === (string) $cid ) {
			return false;
		}

		return parent::add_attachment_to_email( $email_id, $attachment_id, $filename, (string) $cid );
	}

	/**
	 * The condition that limits email_attachments rows to embeds.
	 *
	 * @return string
	 */
	protected function cid_clause() {
		return "email_attachments.cid <> ''";
	}

}
