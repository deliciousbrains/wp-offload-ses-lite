<?php
/**
 * Handle settings for WP Offload SES
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

/**
 * Class Settings
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * The settings key used in the database.
	 *
	 * @var string
	 */
	private $settings_key;

	/**
	 * The settings constant used in defines.
	 *
	 * @var string
	 */
	private $settings_constant;

	/**
	 * The settings array.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Settings that have been defined via constants.
	 *
	 * @var array
	 */
	private $defined_settings;

	/**
	 * Settings that have been defined at the network level.
	 *
	 * @var array
	 */
	private $network_settings;

	/**
	 * Construct the Settings class.
	 *
	 * @param string $settings_key      The settings key used in the database.
	 * @param string $settings_constant The settings constant used in defines.
	 */
	public function __construct( string $settings_key, string $settings_constant ) {
		$this->settings_key      = $settings_key;
		$this->settings_constant = $settings_constant;
	}

	/**
	 * Get the plugin's settings array
	 *
	 * @param bool $force Get the settings fresh.
	 *
	 * @return array
	 */
	public function get_settings( bool $force = false ): array {
		if ( is_null( $this->settings ) || $force ) {
			if ( is_multisite() ) {
				$network_settings = $this->get_network_settings( $force );

				if ( Utils::is_network_admin() ) {
					$this->settings = $network_settings;
				} else {
					$subsite_settings = get_option( $this->settings_key, array() );
					$this->settings   = $this->filter_settings( $network_settings, $subsite_settings );
				}
			} else {
				$this->settings = $this->filter_settings( get_option( $this->settings_key, array() ) );
			}
		}

		return $this->settings;
	}

	/**
	 * Helper function for getting the network-level settings.
	 *
	 * @param bool $force Get the network settings fresh.
	 *
	 * @return array
	 */
	public function get_network_settings( bool $force = false ): array {
		if ( is_null( $this->network_settings ) || $force ) {
			$this->network_settings = $this->filter_settings( get_site_option( $this->settings_key, array() ) );
		}

		return $this->network_settings;
	}

	/**
	 * Get all settings that have been defined via constant for the plugin
	 *
	 * @param bool $force Get the settings fresh.
	 *
	 * @return array
	 */
	public function get_defined_settings( bool $force = false ): array {
		if ( is_null( $this->defined_settings ) || $force ) {
			$this->defined_settings = array();

			$unserialized = array();

			if ( defined( $this->settings_constant ) ) {
				$unserialized = Utils::maybe_unserialize( constant( $this->settings_constant ) );
				$unserialized = is_array( $unserialized ) ? $unserialized : array();
			}

			foreach ( $unserialized as $key => $value ) {
				if ( ! in_array( $key, $this->get_settings_whitelist() ) ) {
					continue;
				}

				if ( is_bool( $value ) || is_null( $value ) ) {
					$value = (int) $value;
				}

				if ( is_numeric( $value ) ) {
					$value = strval( $value );
				} else {
					$value = $this->sanitize_setting( $key, $value );
				}

				$this->defined_settings[ $key ] = $value;
			}

			$this->defined_settings = array_merge( $this->defined_settings, $this->get_wpses_defined_settings() );

			$this->listen_for_settings_constant_changes();

			// Normalize the defined settings before saving, so we can detect when a real change happens.
			ksort( $this->defined_settings );
			update_site_option( 'wposes_constant_' . $this->settings_constant, $this->defined_settings );
		}

		return $this->defined_settings;
	}

	/**
	 * Gets any settings defined using WP SES constants.
	 *
	 * @return array
	 */
	public function get_wpses_defined_settings(): array {
		$constants = array(
			'aws-access-key-id'     => 'WP_SES_ACCESS_KEY',
			'aws-secret-access-key' => 'WP_SES_SECRET_KEY',
			'default-email'         => 'WP_SES_FROM',
			'return-path'           => 'WP_SES_RETURNPATH',
			'reply-to'              => 'WP_SES_REPLYTO',
			'send-via-ses'          => 'WP_SES_AUTOACTIVATE',
			'region'                => 'WP_SES_ENDPOINT',
		);

		foreach ( $constants as $key => $constant ) {
			if ( ! defined( $constant ) ) {
				unset( $constants[ $key ] );
				continue;
			}

			$value = constant( $constant );

			if ( 'region' === $key ) {
				$value  = explode( '.', $value );
				$region = $value[1];
				$value  = $region;
			}

			$constants[ $key ] = $value;
		}

		return $constants;
	}

	/**
	 * Gets a single setting that has been defined in the plugin settings constant
	 *
	 * @param string $key     The key of the setting to get.
	 * @param mixed  $default Default to use if not found.
	 *
	 * @return mixed
	 */
	public function get_defined_setting( string $key, $default = '' ) {
		$defined_settings = $this->get_defined_settings();

		return $defined_settings[ $key ] ?? $default;
	}

	/**
	 * Subscribe to changes of the site option used to store the constant-defined settings.
	 */
	protected function listen_for_settings_constant_changes() {
		if ( ! has_action( 'update_site_option_' . 'wposes_constant_' . $this->settings_constant, array(
			$this,
			'settings_constant_changed',
		) ) ) {
			add_action( 'add_site_option_' . 'wposes_constant_' . $this->settings_constant, array(
				$this,
				'settings_constant_added',
			), 10, 3 );
			add_action( 'update_site_option_' . 'wposes_constant_' . $this->settings_constant, array(
				$this,
				'settings_constant_changed',
			), 10, 4 );
		}
	}

	/**
	 * Translate a settings constant option addition into a change.
	 *
	 * @param string $option     Name of the option.
	 * @param mixed  $value      Value the option is being initialized with.
	 * @param int    $network_id ID of the network.
	 */
	public function settings_constant_added( string $option, $value, int $network_id ) {
		$db_settings = get_site_option( $this->settings_key, array() );
		$this->settings_constant_changed( $option, $value, $db_settings, $network_id );
	}

	/**
	 * Callback for announcing when settings-defined values change.
	 *
	 * @param string $option       Name of the option.
	 * @param mixed  $new_settings Current value of the option.
	 * @param mixed  $old_settings Old value of the option.
	 * @param int    $network_id   ID of the network.
	 */
	public function settings_constant_changed( string $option, $new_settings, $old_settings, int $network_id ) {
		$old_settings = $old_settings ?: array();

		foreach ( $this->get_settings_whitelist() as $setting ) {
			$old_value = isset( $old_settings[ $setting ] ) ? $old_settings[ $setting ] : null;
			$new_value = isset( $new_settings[ $setting ] ) ? $new_settings[ $setting ] : null;

			if ( $old_value !== $new_value ) {
				/**
				 * Setting-specific hook for setting change.
				 *
				 * @param mixed  $new_value
				 * @param mixed  $old_value
				 * @param string $setting
				 */
				do_action(
					'wposes_constant_' . $this->settings_constant . '_changed_' . $setting,
					$new_value,
					$old_value,
					$setting
				);

				/**
				 * Generic hook for setting change.
				 *
				 * @param mixed  $new_value
				 * @param mixed  $old_value
				 * @param string $setting
				 */
				do_action(
					'wposes_constant_' . $this->settings_constant . '_changed',
					$new_value,
					$old_value,
					$setting
				);
			}
		}
	}

	/**
	 * Filter the plugin settings array.
	 *
	 * @param array $settings         The single site/network admin settings to filter.
	 * @param array $subsite_settings Optional settings for a subsite.
	 *
	 * @return array $settings
	 */
	public function filter_settings( array $settings, array $subsite_settings = array() ): array {
		$defined_settings = $this->get_defined_settings();

		// Maybe add subsite settings.
		if ( is_multisite() && ! Utils::is_network_admin() ) {
			$subsite_settings_enabled = false;

			if ( isset( $settings['enable-subsite-settings'] ) ) {
				$subsite_settings_enabled = $settings['enable-subsite-settings'];
			}

			if ( isset( $defined_settings['enable-subsite-settings'] ) ) {
				$subsite_settings_enabled = $defined_settings['enable-subsite-settings'];
			}

			if ( $subsite_settings_enabled ) {
				$settings = array_merge( $settings, $subsite_settings );
			}
		}

		if ( empty( $defined_settings ) ) {
			return $settings;
		}

		foreach ( $defined_settings as $key => $value ) {
			$settings[ $key ] = $value;
		}

		return $settings;
	}

	/**
	 * Get the whitelisted settings for the plugin.
	 *
	 * @param array $settings_whitelist Default whitelist.
	 *
	 * @return array
	 */
	public function get_settings_whitelist( array $settings_whitelist = array() ): array {
		if ( empty( $settings_whitelist ) ) {
			$settings_whitelist = array(
				'send-via-ses',
				'enqueue-only',
				'region',
				'default-email',
				'default-email-name',
				'reply-to',
				'return-path',
				'log-duration',
				'delete-successful',
				'delete-re-sent-failed',
				'completed-setup',
				'enable-open-tracking',
				'enable-click-tracking',
				'enable-subsite-settings',
				'override-network-settings',
				'enable-health-report',
				'health-report-frequency',
				'health-report-recipients',
				'health-report-custom-recipients',
			);
		}

		return apply_filters( 'wposes_settings_whitelist', $settings_whitelist );
	}

	/**
	 * List of settings that should skip full sanitize.
	 *
	 * @param array $skip_sanitize_settings Settings to skip sanitization.
	 *
	 * @return array
	 */
	public function get_skip_sanitize_settings( array $skip_sanitize_settings = array() ): array {
		return apply_filters( 'wposes_skip_sanitize_settings', $skip_sanitize_settings );
	}

	/**
	 * Sanitize a setting value, maybe.
	 *
	 * @param string $key   Setting to sanitize.
	 * @param mixed  $value Value of setting to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_setting( string $key, $value ): string {
		$skip_sanitize = $this->get_skip_sanitize_settings();
		if ( in_array( $key, $skip_sanitize ) ) {
			$value = wp_strip_all_tags( $value );
		} else {
			$value = sanitize_text_field( $value );
		}

		return $value;
	}

	/**
	 * Get a specific setting.
	 *
	 * @param string $key     The key of the setting to get.
	 * @param mixed  $default The default value if not found.
	 *
	 * @return mixed
	 */
	public function get_setting( string $key, $default = '' ) {
		$this->get_settings();
		$setting = $this->settings[ $key ] ?? $default;

		return stripslashes_deep( apply_filters( 'wposes_get_setting', $setting, $key ) );
	}

	/**
	 * Get a specific network setting.
	 *
	 * @param string $key     The key of the setting to get.
	 * @param mixed  $default The default value if not found.
	 *
	 * @return mixed
	 */
	public function get_network_setting( string $key, $default = '' ) {
		$this->get_network_settings();
		$network_setting = $this->network_settings[ $key ] ?? $default;

		return stripslashes_deep( apply_filters( 'wposes_get_network_setting', $network_setting, $key ) );
	}

	/**
	 * Gets arguments used to render a setting view.
	 *
	 * @param string $key Key of the setting.
	 *
	 * @return array
	 */
	public function get_setting_args( string $key ): array {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$is_defined = $this->get_defined_setting( $key, false );

		$args = array(
			'key'           => $key,
			'disabled'      => false,
			'disabled_attr' => '',
			'tr_class'      => str_replace(
				'_',
				'-',
				$wp_offload_ses->get_plugin_prefix() . '-' . $key . '-container'
			),
			'setting_msg'   => '',
			'is_defined'    => false,
		);

		if ( false !== $is_defined ) {
			$args['is_defined']    = true;
			$args['disabled']      = true;
			$args['disabled_attr'] = 'disabled="disabled"';
			$args['tr_class']      .= ' wposes-defined-setting';
			$args['setting_msg']   = '<span class="wposes-defined-in-config">' . __(
					'defined in wp-config.php',
					'wp-offload-ses'
				) . '</span>';
		}

		return $args;
	}

	/**
	 * Delete a setting.
	 *
	 * @param string $key The key of the setting to delete.
	 */
	public function remove_setting( string $key ) {
		$this->get_settings();
		if ( isset( $this->settings[ $key ] ) ) {
			unset( $this->settings[ $key ] );
		}
	}

	/**
	 * Delete a network setting.
	 *
	 * @param string $key The key of the setting to delete.
	 */
	public function remove_network_setting( $key ) {
		$this->get_network_settings();
		if ( isset( $this->network_settings[ $key ] ) ) {
			unset( $this->network_settings[ $key ] );
		}
	}

	/**
	 * Set up any default settings, after the plugin
	 * has loaded and any migration/upgrade routines have run.
	 *
	 * @return bool
	 */
	public function set_default_settings(): bool {
		global $wp_offload_ses;

		// Set up weekly health reports for lite.
		if ( ! $wp_offload_ses->is_pro() && ( ! is_multisite() || Utils::is_network_admin() ) ) {
			if ( '' === $this->get_setting( 'enable-health-report', '' ) ) {
				$this->set_setting( 'enable-health-report', true );
				$this->set_setting( 'health-report-recipients', 'site-admins' );
				$this->set_setting( 'health-report-frequency', 'weekly' );
			}
		}

		// Set the default log duration.
		if ( false === $this->get_setting( 'log-duration', false ) ) {
			$this->set_setting( 'log-duration', 90 );
		}

		$this->save_settings();

		return true;
	}

	/**
	 * Set a setting.
	 *
	 * @param string $key   The key of the setting to set.
	 * @param mixed  $value The value of the setting to set.
	 */
	public function set_setting( string $key, $value ) {
		$this->get_settings();

		$this->settings[ $key ] = $value;
	}

	/**
	 * Set a network setting.
	 *
	 * @param string $key   The key of the setting to set.
	 * @param mixed  $value The value of the setting to set.
	 */
	public function set_network_setting( string $key, $value ) {
		$this->get_network_settings();

		$this->network_settings[ $key ] = $value;
	}

	/**
	 * Bulk set the settings array.
	 *
	 * @param array $settings The settings to set.
	 */
	public function set_settings( array $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Bulk set the network settings array.
	 *
	 * @param array $network_settings The settings to set.
	 */
	public function set_network_settings( array $network_settings ) {
		$this->network_settings = $network_settings;
	}

	/**
	 * Save the settings to the database.
	 */
	public function save_settings() {
		if ( is_array( $this->settings ) ) {
			ksort( $this->settings );
		}

		$this->update_site_option( $this->settings_key, $this->settings );
	}

	/**
	 * Update site option.
	 *
	 * @param string $option   The key of the option to update.
	 * @param mixed  $value    The value of the option.
	 * @param bool   $autoload If it should autoload.
	 *
	 * @return bool
	 */
	public function update_site_option( string $option, $value, bool $autoload = true ): bool {
		if ( is_multisite() && Utils::is_network_admin() ) {
			return update_site_option( $option, $value );
		}

		return update_option( $option, $value, $autoload );
	}
}
