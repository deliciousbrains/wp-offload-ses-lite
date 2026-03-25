<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$value    = ( isset( $value ) ) ? $value : $this->settings->get_setting( $key );
$class    = ( isset( $class ) ) ? 'class="' . esc_attr( $class ) . '"' : '';
$disabled = ( isset( $disabled ) && $disabled ) ? ' disabled' : '';
$values   = ( isset( $values ) && is_array( $values ) && 2 === count( $values ) ) ? $values : array( 0, 1 );
$prefix   = $this->get_plugin_prefix_slug();
$value    = ( ! empty( $value ) ) ? $value : $values[0]; // Default to "Off" for empty/falsy type values.
?>
<div id="<?php echo esc_attr( $prefix . '-' . $key ); ?>-wrap" data-checkbox="<?php echo esc_attr( $prefix . '-' . $key ); ?>" class="wposes-switch<?php echo esc_attr( $disabled . ( $value == $values[1] ? ' on' : '' ) ); ?>">
	<span class="off <?php echo esc_attr( $value == $values[0] ? 'checked' : '' ); ?>">OFF</span>
	<span class="on <?php echo esc_attr( $value == $values[1] ? 'checked' : '' ); ?>">ON</span>
	<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $values[0] ); ?>" />
	<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $values[1] ); ?>" id="<?php echo esc_attr( $prefix . '-' . $key ); ?>" <?php checked( $value, $values[1] ); ?> <?php echo wp_kses_post( $class ); ?>/>
</div>
