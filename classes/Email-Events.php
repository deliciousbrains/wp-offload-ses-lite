<?php
/**
 * Email tracking class for WP Offload SES
 *
 * @package WP Offload SES
 * @author Delicious Brains
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\Email_Log;
use \WP_REST_Request;
use \WP_REST_Response;
use \WP_Error;

/**
 * Class Email_Events
 *
 * @since 1.0.0
 */
class Email_Events {

	/**
	 * The Email_Log class.
	 *
	 * @var Email_Log
	 */
	private $log;

	/**
	 * The table to log email clicks.
	 *
	 * @var string
	 */
	private $clicks_table;

	/**
	 * The email log table.
	 *
	 * @var string
	 */
	private $emails_table;

	/**
	 * The WordPress database class.
	 *
	 * @var \WPDB
	 */
	private $database;

	/**
	 * The secret key used for HMAC authentication.
	 *
	 * @var string
	 */
	private $secret_key;

	/**
	 * Constructor for Email_Events class.
	 */
	public function __construct() {
		global $wpdb;

		$this->log          = new Email_Log();
		$this->database     = $wpdb;
		$this->clicks_table = $this->database->base_prefix . 'oses_clicks';
		$this->emails_table = $this->database->base_prefix . 'oses_emails';

		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Registers the REST routes for click/open tracking.
	 */
	public function register_rest_routes() {
		register_rest_route(
			'wp-offload-ses/v1',
			'/c/(?P<data>\S+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'update_clicks' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'wp-offload-ses/v1',
			'/o/(?P<data>\S+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'update_opens' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Add a link to the database.
	 *
	 * @param int    $email_id The ID of the email to associate it to.
	 * @param string $link     The URL to add.
	 *
	 * @return int|bool
	 */
	public function add_link( $email_id, $link ) {
		$result = $this->database->insert(
			$this->clicks_table,
			array(
				'email_id'        => $email_id,
				'email_click_url' => $link,
			)
		);

		if ( ! $result ) {
			return false;
		}

		return $this->database->insert_id;
	}

	/**
	 * Delete links associated with the provided email id.
	 *
	 * @param int $email_id The email id to delete links for.
	 *
	 * @return int|bool
	 */
	public function delete_links_by_email( $email_id ) {
		return $this->database->delete(
			$this->clicks_table,
			array(
				'email_id' => $email_id,
			),
			array(
				'%d',
			)
		);
	}

	/**
	 * Get the number of clicks & last click date for an email.
	 *
	 * @param int $email_id The email ID.
	 *
	 * @return array|bool
	 */
	public function get_email_click_data( $email_id ) {
		$query = $this->database->prepare( "SELECT SUM(email_click_count) AS email_click_count, MAX(email_last_click_date) AS email_last_click_date FROM $this->clicks_table WHERE email_id = %d", $email_id );

		return $this->database->get_row( $query, ARRAY_A );
	}

	/**
	 * Gets the tracking URL for link clicks.
	 *
	 * @param int    $email_id       The ID of the email.
	 * @param int    $email_click_id The ID of the email link.
	 * @param string $url            The original URL.
	 *
	 * @return string
	 */
	private function get_click_tracking_url( $email_id, $email_click_id, $url ) {
		$hash       = $this->generate_hmac_hash( $email_id . $email_click_id . $url );
		$url_string = base64_encode( 'email_id=' . $email_id . '&email_click_id=' . $email_click_id . '&email_click_url=' . urlencode( $url ) . '&hash=' . $hash );

		return get_rest_url( null, '/wp-offload-ses/v1/c/' . $url_string );
	}

	/**
	 * Gets the URL for tracking email opens.
	 *
	 * @param int $email_id The ID of the email.
	 *
	 * @return string
	 */
	private function get_open_tracking_url( $email_id ) {
		$hash       = $this->generate_hmac_hash( $email_id );
		$url_string = base64_encode( 'email_id=' . $email_id . '&hash=' . $hash );

		return get_rest_url( null, '/wp-offload-ses/v1/o/' . $url_string );
	}

	/**
	 * Filter email content to enable tracking.
	 *
	 * @param int    $email_id The ID of the email being filtered.
	 * @param string $content  The email content to filter.
	 *
	 * @return string
	 */
	public function filter_email_content( $email_id, $content ) {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$open_tracking  = (bool) $wp_offload_ses->settings->get_setting( 'enable-open-tracking' );
		$click_tracking = (bool) $wp_offload_ses->settings->get_setting( 'enable-click-tracking' );

		if ( $open_tracking ) {
			// Add the tracking pixel.
			$content .= '<img src="' . $this->get_open_tracking_url( $email_id ) . '" alt="" />';
		}

		if ( ! $click_tracking ) {
			return $content;
		}

		$dom = new \DOMDocument();

		$libxml_previous_state = libxml_use_internal_errors( true );

		if ( function_exists( 'mb_convert_encoding' ) ) {
			$content = mb_convert_encoding( $content, 'HTML-ENTITIES', get_bloginfo( 'charset' ) );
		}

		$dom->loadHTML( $content );

		$links = $dom->getElementsByTagName( 'a' );

		foreach ( $links as $link ) {
			$url = $link->getAttribute( 'href' );

			if ( '' === $url || '#' === substr( $url, 0, 1 ) ) {
				continue;
			}

			// Store the link in the database.
			$email_click_id = $this->add_link( $email_id, $url );

			if ( false === $email_click_id ) {
				continue;
			}

			// Change the href to our internal URL.
			$link->setAttribute( 'href', $this->get_click_tracking_url( $email_id, $email_click_id, $url ) );
		}

		$saved = $dom->saveHTML();

		if ( false !== $saved ) {
			$content = $saved;
		}

		libxml_clear_errors();
		libxml_use_internal_errors( $libxml_previous_state );

		return $content;
	}

	/**
	 * Updates the click count for the email.
	 *
	 * @param WP_REST_Request $request The request object.
	 */
	public function update_clicks( WP_REST_Request $request ) {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$data     = $request['data'];
		$data     = base64_decode( $data );
		$response = new WP_REST_Response();
		$args     = array();

		parse_str( $data, $args );

		if ( ! isset( $args['email_id'] ) || ! isset( $args['email_click_id'] ) || ! isset( $args['hash'] ) || ! isset( $args['email_click_url'] ) ) {
			return new WP_Error( 'invalid_request', __( 'The request you made was invalid.', 'wp-offload-ses' ), array( 'status' => 404 ) );
		}

		// Verify the HMAC hash matches what we expect.
		if ( ! $this->verify_hmac_hash( $args['email_id'] . $args['email_click_id'] . $args['email_click_url'], $args['hash'] ) ) {
			return new WP_Error( 'invalid_request', __( 'The request you made was invalid.', 'wp-offload-ses' ), array( 'status' => 404 ) );
		}

		// Log the click. We have to run the query manually to increment the event count.
		$time   = current_time( 'mysql' );
		$query  = $this->database->prepare( "UPDATE {$this->clicks_table} SET email_click_count = email_click_count + 1, email_first_click_date = IFNULL(email_first_click_date, %s), email_last_click_date = %s WHERE email_click_id = %d", $time, $time, $args['email_click_id'] );
		$result = $this->database->query( $query );

		// Increment the opens if there are no logged opens (due to disabled images).
		if ( $wp_offload_ses->settings->get_setting( 'enable-open-tracking', false ) ) {
			$query  = $this->database->prepare( "UPDATE {$this->emails_table} SET email_open_count = 1, email_first_open_date = %s, email_last_open_date = %s WHERE email_id = %d AND email_open_count = 0", $time, $time, $args['email_id'] );
			$result = $this->database->query( $query );
		}

		// Redirect.
		$response->header( 'Cache-Control', 'no-store, no-chache, must-revalidate, max-age=0' );
		$response->header( 'Pragma', 'no-cache' );
		$response->set_status( 301 );
		$response->header( 'Location', urldecode( $args['email_click_url'] ) );
		return $response;
	}

	/**
	 * Updates the open count for the email.
	 *
	 * @param WP_REST_Request $request The request object.
	 */
	public function update_opens( WP_REST_Request $request ) {
		// Get the email ID.
		$data = $request['data'];
		$data = base64_decode( $data );
		$args = array();

		parse_str( $data, $args );

		if ( ! isset( $args['email_id'] ) || ! isset( $args['hash'] ) ) {
			return new WP_Error( 'invalid_request', __( 'The request you made was invalid.', 'wp-offload-ses' ), array( 'status' => 404 ) );
		}

		// Verify the HMAC hash matches what we expect.
		if ( ! $this->verify_hmac_hash( $args['email_id'], $args['hash'] ) ) {
			return new WP_Error( 'invalid_request', __( 'The request you made was invalid.', 'wp-offload-ses' ), array( 'status' => 404 ) );
		}

		// Log the open.
		$time   = current_time( 'mysql' );
		$query  = $this->database->prepare( "UPDATE {$this->emails_table} SET email_open_count = email_open_count + 1, email_first_open_date = IFNULL(email_first_open_date, %s), email_last_open_date = %s WHERE email_id = %d", $time, $time, $args['email_id'] );
		$result = $this->database->query( $query );

		// Display the tracking pixel used in the email.
		header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
		header( 'Pragma: no-cache' );
		header( 'Content-Type: image/gif' );
		echo base64_decode( 'R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==' ); // phpcs:ignore
		exit;
	}

	/**
	 * Generates a HMAC hash.
	 *
	 * @param string $data The data to be hashed.
	 *
	 * @return string
	 */
	private function generate_hmac_hash( $data ) {
		return hash_hmac( 'sha256', $data, $this->get_secret_key(), false );
	}

	/**
	 * Compare the HMAC hashes and verify they are the same.
	 *
	 * @param string $data The data to store in the correct hash.
	 * @param string $hash The hash to compare against.
	 *
	 * @return bool
	 */
	private function verify_hmac_hash( $data, $hash ) {
		$expected = $this->generate_hmac_hash( $data );
		$actual   = $hash;

		/**
		 * The `hash_equals()` function only exists on PHP 5.6+,
		 * but WordPress creates it for us if it doesn't exist.
		 */
		if ( ! hash_equals( $expected, $actual ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Gets the secret key used in HMAC hashes.
	 *
	 * @return string
	 */
	private function get_secret_key() {
		if ( ! is_null( $this->secret_key ) ) {
			return $this->secret_key;
		}

		$this->secret_key = get_site_option( 'wposes_tracking_key' );

		return $this->secret_key;
	}

	/**
	 * Install/update the email events table(s) if necessary.
	 */
	public function install_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$wpdb->hide_errors();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$this->clicks_table} (
				`email_click_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`email_id` bigint(20) NOT NULL,
				`email_click_url` VARCHAR(255),
				`email_click_count` INT DEFAULT '0',
				`email_first_click_date` datetime,
				`email_last_click_date` datetime,
				PRIMARY KEY (email_click_id),
				INDEX email_id (email_id)
				) $charset_collate;";
		dbDelta( $sql );
	}

}
