<?php
$value    = ( isset( $value ) ) ? $value : $this->settings->get_setting( $key );
$class    = ( isset( $class ) ) ? 'class="' . $class . '"' : '';
$disabled = ( isset( $disabled ) && $disabled ) ? ' disabled' : '';
$values   = ( isset( $values ) && is_array( $values ) && 2 === count( $values ) ) ? $values : array( 0, 1 );
$prefix   = $this->get_plugin_prefix_slug();
$value    = ( ! empty( $value ) ) ? $value : $values[0]; // Default to "Off" for empty/falsy type values.
?>
<div id="<?php echo $prefix . '-' . $key; ?>-wrap" data-checkbox="<?php echo $prefix . '-' . $key; ?>" class="wposes-switch<?php echo $disabled . ( $value == $values[1] ? ' on' : '' ); ?>">
	<span class="off <?php echo $value == $values[0] ? 'checked' : ''; ?>">OFF</span>
	<span class="on <?php echo $value == $values[1] ? 'checked' : ''; ?>">ON</span>
	<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $values[0]; ?>" />
	<input type="checkbox" name="<?php echo $key; ?>" value="<?php echo $values[1]; ?>" id="<?php echo $prefix . '-' . $key; ?>" <?php echo $value == $values[1] ? 'checked="checked" ' : ''; ?> <?php echo $class ?>/>
</div>
