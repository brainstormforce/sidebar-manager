<?php
/**
 * White Label Form
 *
 * @package Astra SideBar
 */

?>
<li>
	<div class="branding-form postbox">
		<button type="button" class="handlediv button-link" aria-expanded="true">
			<span class="screen-reader-text"><?php _e( 'Astra SideBar Branding', 'sidebar-manager' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		<h2 class="hndle ui-sortable-handle">
			<span><?php _e( 'Astra SideBar Branding', 'sidebar-manager' ); ?></span>
		</h2>
		<div class="inside">
			<div class="form-wrap">
				<div class="form-field">
					<label><?php _e( 'Plugin Name:', 'sidebar-manager' ); ?>
						<input type="text" name="ast_white_label[bsf-lw-sb][name]" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['bsf-lw-sb']['name'] ); ?>">
					</label>
				</div>
				<div class="form-field">
					<label><?php _e( 'Plugin Description:', 'sidebar-manager' ); ?>
						<textarea name="ast_white_label[bsf-lw-sb][description]" class="placeholder placeholder-active" rows="2"><?php echo esc_attr( $settings['bsf-lw-sb']['description'] ); ?></textarea>
					</label>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</li>
