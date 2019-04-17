<h2 class="nav-tab-wrapper">
	<div class="nav-tab-container">
		<?php
		foreach ( $this->get_settings_tabs() as $tab => $label ) : ?>
			<a href="#<?php echo $tab; ?>" class="nav-tab js-action-link <?php echo $tab; ?>" data-tab="<?php echo $tab; ?>">
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
	</div>
</h2>
<div id="wposes-settings-sub-nav" class="sub-nav-tab-container" style="display: none;">
	<?php
	foreach ( $this->get_settings_sub_nav_tabs() as $tab => $label ) : ?>
		<a href="#<?php echo $tab; ?>" title="<?php echo esc_html( $label ); ?>" class="nav-tab js-action-link <?php echo $tab; ?>" data-tab="<?php echo $tab; ?>">
			<?php echo esc_html( $label ); ?>
		</a>
	<?php endforeach; ?>
</div>