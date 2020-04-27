<?php
/**
 * Handles attachments for WP Offload SES.
 *
 * @author Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

/**
 * Class Attachments
 *
 * @since 1.4.1
 */
class Attachments {

	/**
	 * The directory to store attachments in.
	 *
	 * @var string
	 */
	private $attachments_dir;

	/**
	 * The URL to the attachments folder.
	 */
	private $attachments_url;

	/**
	 * The attachments table.
	 *
	 * @var string
	 */
	private $attachments_table;

	/**
	 * The email attachments table.
	 *
	 * @var string
	 */
	private $email_attachments_table;

	/**
	 * The WordPress database class.
	 *
	 * @var \WPDB
	 */
	private $database;

	/**
	 * Construct the Attachments class.
	 */
	public function __construct() {
		global $wpdb;

		$uploads_dir           = wp_upload_dir();
		$this->attachments_dir = $uploads_dir['basedir'] . '/wp-offload-ses/';
		$this->attachments_url = $uploads_dir['baseurl'] . '/wp-offload-ses/';
		$this->database        = $wpdb;

		$this->attachments_table       = $this->database->base_prefix . 'oses_attachments';
		$this->email_attachments_table = $this->database->base_prefix . 'oses_email_attachments';
	}

	/**
	 * Get the hash of the file to verify uniqueness.
	 *
	 * @param string $file_path The path of the file to check.
	 *
	 * @return string|bool
	 */
	public function get_hash( $file_path ) {
		return @md5_file( $file_path );
	}

	/**
	 * Get the filename of the attachment.
	 *
	 * @param string $file_path The path of the file.
	 *
	 * @return string|bool
	 */
	public function get_filename( $file_path ) {
		return wp_basename( $file_path );
	}

	/**
	 * Get the salt used to prefix the copied file.
	 *
	 * @return string
	 */
	public function get_salt() {
		return wp_generate_password( 6, false, false );
	}

	/**
	 * Checks if the attachment already exists.
	 *
	 * @param string $hash The path to the original attachment.
	 *
	 * @return array|bool
	 */
	public function attachment_exists( $hash ) {
		$query  = $this->database->prepare( "SELECT id, path FROM {$this->attachments_table} WHERE hash = '%s'", $hash );
		$result = $this->database->get_row( $query, ARRAY_A );

		if ( ! $result ) {
			return false;
		}

		return $result;
	}

	/**
	 * Stores the file in our uploads directory.
	 *
	 * @param string $file_path The path to the original attachment.
	 *
	 * @return string|bool
	 */
	public function copy_file( $file_path ) {
		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		$salt     = $this->get_salt();
		$new_path = $this->attachments_dir . $salt . '/' . $this->get_filename( $file_path );

		if ( ! is_dir( $this->attachments_dir . $salt ) ) {
			wp_mkdir_p( $this->attachments_dir . $salt );
		}

		if ( copy( $file_path, $new_path ) ) {
			return $new_path;
		}

		return false;
	}

	/**
	 * Create the attachment in the attachments table.
	 *
	 * @param string $hash The hash of the file contents.
	 * @param string $path The path of the file.
	 *
	 * @return int|bool
	 */
	public function create_attachment( $hash, $path ) {
		$result = $this->database->insert(
			$this->attachments_table,
			array(
				'hash' => $hash,
				'path' => $path,
			),
			'%s'
		);

		if ( ! $result ) {
			return false;
		}

		return $this->database->insert_id;
	}

	/**
	 * Processes an attachment, storing it in our custom folder
	 * and the database as necessary.
	 *
	 * @param int    $email_id   The ID of the email sending the attachment.
	 * @param string $attachment The path to the attachment.
	 *
	 * @return bool
	 */
	public function handle_attachment( $email_id, $attachment ) {
		if ( ! file_exists( $attachment ) ) {
			return false;
		}

		$hash = $this->get_hash( $attachment );

		if ( ! $hash ) {
			return false;
		}

		$stored = $this->attachment_exists( $hash );

		if ( $stored ) {
			$id       = $stored['id'];
			$new_path = $stored['path'];
		} else {
			$new_path = $this->copy_file( $attachment );
			$id       = $this->create_attachment( $hash, $new_path );
		}

		if ( ! $new_path || ! $id ) {
			return false;
		}

		return $this->add_attachment_to_email( $email_id, $id, $this->get_filename( $attachment ) );
	}

	/**
	 * Adds the attachment to the provided email.
	 *
	 * @param int    $email_id      The ID of the email to add the attachment to.
	 * @param int    $attachment_id The ID of the attachment to add.
	 * @param string $filename      The filename to use.
	 *
	 * @return bool
	 */
	public function add_attachment_to_email( $email_id, $attachment_id, $filename ) {
		$result = $this->database->insert(
			$this->email_attachments_table,
			array(
				'email_id'      => $email_id,
				'attachment_id' => $attachment_id,
				'filename'      => $filename,
			),
			array(
				'%d',
				'%d',
				'%s'
			)
		);

		return (bool) $result;
	}

	/**
	 * Gets an array of attachments for a provided email.
	 *
	 * @param int $email_id The ID of the email.
	 *
	 * @return array|bool
	 */
	public function get_attachments_by_email( $email_id ) {
		$query = $this->database->prepare(
			"SELECT email_attachments.filename AS filename, attachments.id AS id, attachments.path AS path
			FROM {$this->email_attachments_table} email_attachments
			INNER JOIN {$this->attachments_table} attachments ON email_attachments.attachment_id = attachments.id
			WHERE email_attachments.email_id = %d;",
			$email_id
		);

		return $this->database->get_results( $query, ARRAY_A );
	}

	/**
	 * Gets the attachment links for an email.
	 *
	 * @param int $email_id The ID of the email.
	 *
	 * @return array|bool
	 */
	public function get_attachment_links( $email_id ) {
		$attachments      = $this->get_attachments_by_email( $email_id );
		$attachment_links = array();

		if ( ! is_array( $attachments ) || empty( $attachments ) ) {
			return false;
		}

		foreach ( $attachments as $attachment ) {
			$attachment_links[] = sprintf(
				'<a href="%1$s" target="_blank">%2$s</a>',
				esc_url( str_replace( $this->attachments_dir, $this->attachments_url, $attachment['path'] ) ),
				esc_html( $attachment['filename'] )
			);
		}

		return $attachment_links;
	}

	/**
	 * Deletes the attachments from an email.
	 *
	 * @param int $email_id The ID of the email to delete attachments for.
	 *
	 * @return bool
	 */
	public function delete_email_attachments( $email_id ) {
		$query = $this->database->prepare(
			"DELETE FROM $this->email_attachments_table
			WHERE $this->email_attachments_table.email_id = %d",
			$email_id
		);

		return (bool) $this->database->query( $query );
	}

	/**
	 * Deletes attachments from the filesystem.
	 *
	 * @return bool
	 */
	public function delete_attachments() {
		$query       = "SELECT id, path FROM {$this->attachments_table} WHERE gc = 1";
		$attachments = $this->database->get_results( $query, ARRAY_A );

		foreach ( $attachments as $attachment ) {
			$file    = $attachment['path'];
			$deleted = false;

			// Not our file, don't delete it.
			if ( $this->attachments_dir !== substr( $file, 0, strlen( $this->attachments_dir ) ) ) {
				continue;
			}

			if ( file_exists( $file ) ) {
				$deleted = @unlink( $file );
			}

			if ( $deleted ) {
				@rmdir( dirname( $file ) );
				$query = $this->database->prepare( "DELETE FROM {$this->attachments_table} WHERE id = %d", $attachment['id'] );
				$this->database->query( $query );
			}			
		}

		return true;
	}

	/**
	 * Installs the necessary database tables.
	 */
	public function install_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$wpdb->hide_errors();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$this->attachments_table} (
				id BIGINT(20) NOT NULL AUTO_INCREMENT,
				hash TEXT NOT NULL,
				path TEXT NOT NULL,
				gc TINYINT(1) DEFAULT '0',
				PRIMARY KEY (id)
				) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE {$this->email_attachments_table} (
				email_id BIGINT(20) NOT NULL,
				attachment_id BIGINT(20),
				filename TEXT,
				UNIQUE KEY uidx_email_attachment (email_id, attachment_id, filename(190))
				) $charset_collate;";
		dbDelta( $sql );
	}

}
