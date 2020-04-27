<?php

namespace DeliciousBrains\WP_Offload_SES;

use \Datetime;

class Utils {

	/**
	 * Trailing slash prefix string ensuring no leading slashes.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function trailingslash_prefix( $string ) {
		return ltrim( trailingslashit( $string ), '/\\' );
	}

	/**
	 * Remove scheme from URL.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public static function remove_scheme( $url ) {
		return preg_replace( '/^(?:http|https):/', '', $url );
	}

	/**
	 * Reduce the given URL down to the simplest version of itself.
	 *
	 * Useful for matching against the full version of the URL in a full-text search
	 * or saving as a key for dictionary type lookup.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public static function reduce_url( $url ) {
		$parts = self::parse_url( $url );
		$host  = isset( $parts['host'] ) ? $parts['host'] : '';
		$port  = isset( $parts['port'] ) ? ":{$parts['port']}" : '';
		$path  = isset( $parts['path'] ) ? $parts['path'] : '';

		return '//' . $host . $port . $path;
	}

	/**
	 * Parses a URL into its components. Compatible with PHP < 5.4.7.
	 *
	 * @param string  $url The URL to parse.
	 * @param int     $component PHP_URL_ constant for URL component to return.
	 *
	 * @return mixed An array of the parsed components, mixed for a requested component, or false on error.
	 */
	public static function parse_url( $url, $component = -1 ) {
		$url       = trim( $url );
		$no_scheme = 0 === strpos( $url, '//' );

		if ( $no_scheme ) {
			$url = 'http:' . $url;
		}

		$parts = parse_url( $url, $component );

		if ( 0 < $component ) {
			return $parts;
		}

		if ( $no_scheme && is_array( $parts ) ) {
			unset( $parts['scheme'] );
		}

		return $parts;
	}

	/**
	 * Is the string a URL?
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	public static function is_url( $string ) {
		if ( preg_match( '@^(?:https?:)?//[a-zA-Z0-9\-]+@', $string ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Is the string a relative URL?
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	public static function is_relative_url( $string ) {
		if ( empty( $string ) || ! is_string( $string ) ) {
			return false;
		}

		$url = static::parse_url( $string );

		return ( empty( $url['scheme'] ) && empty( $url['host'] ) );
	}

	/**
	 * Create a site link for given URL.
	 *
	 * @param string $url
	 * @param string $text
	 *
	 * @return string
	 */
	public static function dbrains_link( $url, $text ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $text ) );
	}

	/**
	 * Check whether two URLs share the same domain.
	 *
	 * @param string $url_a
	 * @param string $url_b
	 *
	 * @return bool
	 */
	public static function url_domains_match( $url_a, $url_b ) {
		if ( ! static::is_url( $url_a ) || ! static::is_url( $url_b ) ) {
			return false;
		}

		return static::parse_url( $url_a, PHP_URL_HOST ) === static::parse_url( $url_b, PHP_URL_HOST );
	}

	/**
	 * Get the current domain.
	 *
	 * @return string|false
	 */
	public static function current_domain() {
		return parse_url( home_url(), PHP_URL_HOST );
	}

	/**
	 * Get the base domain of the current domain.
	 *
	 * @return string
	 */
	public static function current_base_domain() {
		$domain = static::current_domain();
		$parts  = explode( '.', $domain, 2 );

		if ( isset( $parts[1] ) && in_array( $parts[0], array( 'www' ) ) ) {
			return $parts[1];
		}

		return $domain;
	}

	/**
	 * Get the first defined constant from the given list of constant names.
	 *
	 * @param array $constants
	 *
	 * @return string|false string constant name if defined, otherwise false if none are defined
	 */
	public static function get_first_defined_constant( $constants ) {
		foreach ( (array) $constants as $constant ) {
			if ( defined( $constant ) ) {
				return $constant;
			}
		}

		return false;
	}

	/**
	 * Get the default email address used by WordPress. Lifted from wp_mail().
	 *
	 * @param bool $apply_filter Set to false to override the `wp_mail_from` filter.
	 *
	 * @return string
	 */
	public static function get_wordpress_default_email( $apply_filter = true ) {
		// Get the site domain and get rid of www.
		$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );

		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;

		if ( ! $apply_filter ) {
			return $from_email;
		}

		return apply_filters( 'wp_mail_from', $from_email );
	}

	/**
	 * Checks if current screen is a network admin screen,
	 * or if the current AJAX request was sent from a network admin screen.
	 *
	 * @return bool
	 */
	public static function is_network_admin() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			/**
			 * Workaround - `is_network_admin()` won't work w/ AJAX.
			 * https://core.trac.wordpress.org/ticket/22589
			 */
			if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], 'wp-admin/network' ) ) {
				return true;
			}

			return false;
		}

		return is_network_admin();
	}

	/**
	 * Gets the formatted date & time from a MySQL timestamp.
	 *
	 * @param string $timestamp The timestamp.
	 *
	 * @return array
	 */
	public static function get_date_and_time( $timestamp ) {
		$datetime    = new Datetime( $timestamp );
		$date_format = str_replace( 'F', 'M', get_option( 'date_format', 'm/d/Y' ) );
		$time_format = get_option( 'time_format', 'g:i a' );

		$date = $datetime->format( $date_format );
		$time = $datetime->format( $time_format );

		if ( ! $date || ! $time ) {
			return false;
		}

		return array(
			'date' => $date,
			'time' => $time,
		);
	}

}
