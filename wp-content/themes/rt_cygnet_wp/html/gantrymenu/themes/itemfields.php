<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class GantryMenuItemFieldsDefault {
	
	public $fields = array(
		'gantrymenu_item_subtext',
		'gantrymenu_customimage',
		'gantrymenu_customicon',
		'gantrymenu_columns',
		'gantrymenu_distribution',
		'gantrymenu_manual_distribution',
		'gantrymenu_children_group',
		'gantrymenu_dropdown_width',
		'gantrymenu_column_widths',
		'gantrymenu_large_direction',
		'gantrymenu_desktop_direction',
		'gantrymenu_tablet_direction'
	);

	protected $icons = array('icon-adjust','icon-align-center','icon-align-justify','icon-align-left','icon-align-right','icon-arrow-down','icon-arrow-left','icon-arrow-right','icon-arrow-up','icon-asterisk','icon-backward','icon-ban-circle','icon-bar-chart','icon-barcode','icon-beaker','icon-bell','icon-bold','icon-bolt','icon-book','icon-bookmark','icon-bookmark-empty','icon-briefcase','icon-bullhorn','icon-calendar','icon-camera','icon-camera-retro','icon-caret-down','icon-caret-left','icon-caret-right','icon-caret-up','icon-certificate','icon-check','icon-check-empty','icon-chevron-down','icon-chevron-left','icon-chevron-right','icon-chevron-up','icon-circle-arrow-down','icon-circle-arrow-left','icon-circle-arrow-right','icon-circle-arrow-up','icon-cloud','icon-cog','icon-cogs','icon-columns','icon-comment','icon-comment-alt','icon-comments','icon-comments-alt','icon-copy','icon-credit-card','icon-cut','icon-dashboard','icon-download','icon-download-alt','icon-edit','icon-eject','icon-envelope','icon-envelope-alt','icon-exclamation-sign','icon-external-link','icon-eye-close','icon-eye-open','icon-facebook','icon-facebook-sign','icon-facetime-video','icon-fast-backward','icon-fast-forward','icon-file','icon-file-text','icon-film','icon-filter','icon-fire','icon-flag','icon-folder-close','icon-folder-open','icon-font','icon-forward','icon-fullscreen','icon-gift','icon-github','icon-github-sign','icon-glass','icon-globe','icon-google-plus','icon-google-plus-sign','icon-group','icon-hand-down','icon-hand-left','icon-hand-right','icon-hand-up','icon-hdd','icon-headphones','icon-heart','icon-heart-empty','icon-home','icon-inbox','icon-indent-left','icon-indent-right','icon-info-sign','icon-italic','icon-key','icon-leaf','icon-legal','icon-lemon','icon-link','icon-linkedin','icon-linkedin-sign','icon-list','icon-list-alt','icon-list-ol','icon-list-ul','icon-lock','icon-magic','icon-magnet','icon-map-marker','icon-minus','icon-minus-sign','icon-money','icon-move','icon-music','icon-off','icon-ok','icon-ok-circle','icon-ok-sign','icon-paper-clip','icon-paste','icon-pause','icon-pencil','icon-phone','icon-phone-sign','icon-picture','icon-pinterest','icon-pinterest-sign','icon-plane','icon-play','icon-play-circle','icon-plus','icon-plus-sign','icon-print','icon-pushpin','icon-qrcode','icon-question-sign','icon-random','icon-refresh','icon-remove','icon-remove-circle','icon-remove-sign','icon-reorder','icon-repeat','icon-resize-full','icon-resize-horizontal','icon-resize-small','icon-resize-vertical','icon-retweet','icon-road','icon-rss','icon-save','icon-screenshot','icon-search','icon-share','icon-share-alt','icon-shopping-cart','icon-sign-blank','icon-signal','icon-signin','icon-signout','icon-sitemap','icon-sort','icon-sort-down','icon-sort-up','icon-star','icon-star-empty','icon-star-half','icon-step-backward','icon-step-forward','icon-stop','icon-strikethrough','icon-table','icon-tag','icon-tags','icon-tasks','icon-text-height','icon-text-width','icon-th','icon-th-large','icon-th-list','icon-thumbs-down','icon-thumbs-up','icon-time','icon-tint','icon-trash','icon-trophy','icon-truck','icon-twitter','icon-twitter-sign','icon-umbrella','icon-underline','icon-undo','icon-unlock','icon-upload','icon-upload-alt','icon-user','icon-user-md','icon-volume-down','icon-volume-off','icon-volume-up','icon-warning-sign','icon-wrench','icon-zoom-in','icon-zoom-out');

	public function renderFields( $item_id, $item, $depth, $args ) {
		global $gantry;
		ob_start();
		?>

		<p class="field-gantrymenu_item_subtext description description-thin">
			<label for="edit-menu-item-gantrymenu_item_subtext-<?php echo $item_id; ?>">
				<?php _e( 'Subtext', 'rt_cygnet_wp_lang' ); ?><br />
				<input type="text" id="edit-menu-item-gantrymenu_item_subtext-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_item_subtext" name="menu-item-gantrymenu_item_subtext[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->gantrymenu_item_subtext ); ?>"/>
			</label>
		</p>

		<p class="field-gantrymenu_customimage description description-thin">
			<label for="edit-menu-item-gantrymenu_customimage-<?php echo $item_id; ?>">
				<?php _e( 'Menu Image', 'rt_cygnet_wp_lang' ); ?><br />
				<select id="edit-menu-item-gantrymenu_customimage-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_customimage" name="menu-item-gantrymenu_customimage[<?php echo $item_id; ?>]">
					<option value="" <?php if( esc_attr( $item->gantrymenu_customimage ) == '' ) : ?>selected="selected"<?php endif;?>><?php _e( '- None Selected -', 'rt_cygnet_wp_lang' ); ?></option>
					<?php
					$icon_path = $gantry->templatePath . '/images/icons';
					$icons = array();
					if( file_exists( $icon_path ) && is_dir( $icon_path ) ) {
						$d = dir( $icon_path );
						while ( false !== ( $entry = $d->read() ) ) {
							if( !preg_match( '/^\./', $entry ) && preg_match( '/\.png$/', $entry ) ) {
								$icon_name = basename( $entry, '.png' );
								$icons[$entry] = $icon_name;
							}
						}
					}
					?>
					<?php foreach( $icons as $iconurl => $iconname ): ?>
						<option value="<?php echo $iconurl; ?>" <?php if( esc_attr( $item->gantrymenu_customimage ) == $iconurl ) : ?>selected="selected"<?php endif;?>><?php echo $iconname; ?></option>
					<?php endforeach;?>
				</select>
			</label>
		</p>

		<p class="field-gantrymenu_customicon description description-thin">
			<label for="edit-menu-item-gantrymenu_customicon-<?php echo $item_id; ?>">
				<?php _e( 'Menu Icon', 'rt_cygnet_wp_lang' ); ?><br />
				<input type="text" id="edit-menu-item-gantrymenu_customicon-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_customicon" name="menu-item-gantrymenu_customicon[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->gantrymenu_customicon ); ?>"/>
			</label>
		</p>

		<p class="field-gantrymenu_columns description description-thin">
			<label for="edit-menu-item-gantrymenu_columns-<?php echo $item_id; ?>">
				<?php _e( 'Number of Columns in Submenu', 'rt_cygnet_wp_lang' ); ?><br />
				<select id="edit-menu-item-gantrymenu_columns-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_columns" name="menu-item-gantrymenu_columns[<?php echo $item_id; ?>]">
					<option value="1"<?php if( esc_attr( $item->gantrymenu_columns ) == 1 ) : ?> selected="selected"<?php endif;?>>1</option>
					<option value="2"<?php if( esc_attr( $item->gantrymenu_columns ) == 2 ) : ?> selected="selected"<?php endif;?>>2</option>
					<option value="3"<?php if( esc_attr( $item->gantrymenu_columns ) == 3 ) : ?> selected="selected"<?php endif;?>>3</option>
					<option value="4"<?php if( esc_attr( $item->gantrymenu_columns ) == 4 ) : ?> selected="selected"<?php endif;?>>4</option>
				</select>
			</label>
		</p>

		<p class="field-gantrymenu_distribution description description-wide">
			<label for="gantrymenu_distribution">
				<?php _e( 'Item Distribution', 'rt_cygnet_wp_lang' ); ?><br />
				<input id="gantrymenu_distributioneven-<?php echo $item_id; ?>" type="radio" value="evenly" name="menu-item-gantrymenu_distribution[<?php echo $item_id; ?>]" <?php if( esc_attr( $item->gantrymenu_distribution ) == 'evenly' || esc_attr( $item->gantrymenu_distribution ) == '' ) : ?> checked="checked"<?php endif;?> />
				<label for="gantrymenu_distributioneven-<?php echo $item_id; ?>"><?php _e( 'Evenly', 'rt_cygnet_wp_lang' ); ?></label>         	
				<input id="gantrymenu_distributionorder-<?php echo $item_id; ?>" type="radio" value="inorder" name="menu-item-gantrymenu_distribution[<?php echo $item_id; ?>]" <?php if( esc_attr( $item->gantrymenu_distribution ) == 'inorder') : ?> checked="checked"<?php endif;?> />
				<label for="gantrymenu_distributionorder-<?php echo $item_id; ?>"><?php _e( 'In Order', 'rt_cygnet_wp_lang' ); ?></label>
				<input id="gantrymenu_distributionmanual-<?php echo $item_id; ?>" type="radio" value="manual" name="menu-item-gantrymenu_distribution[<?php echo $item_id; ?>]" <?php if( esc_attr( $item->gantrymenu_distribution ) == 'manual') : ?> checked="checked"<?php endif;?> />
				<label for="gantrymenu_distributionmanual-<?php echo $item_id; ?>"><?php _e( 'Manually', 'rt_cygnet_wp_lang' ); ?></label>
			</label>
		</p>

		<p class="field-gantrymenu_manual_distribution description description-thin">
			<label for="edit-menu-item-gantrymenu_manual_distribution-<?php echo $item_id; ?>">
				<?php _e( 'Manual Item Distribution', 'rt_cygnet_wp_lang' ); ?><br/>
				<input type="text" id="edit-menu-item-gantrymenu_manual_distribution-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_manual_distribution" name="menu-item-gantrymenu_manual_distribution[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->gantrymenu_manual_distribution); ?>"/>
			</label>
		</p>

		<p class="field-gantrymenu_children_group description description-thin">
			<label for="edit-menu-item-gantrymenu_children_group-<?php echo $item_id; ?>">
				<?php _e( 'Group Child Items', 'rt_cygnet_wp_lang' ); ?><br />
				<select id="edit-menu-item-gantrymenu_children_group-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_children_group" name="menu-item-gantrymenu_children_group[<?php echo $item_id; ?>]">
					<option value="0"<?php if( esc_attr( $item->gantrymenu_children_group ) == 0 ) : ?> selected="selected"<?php endif;?>><?php _e( 'No', 'rt_cygnet_wp_lang' ); ?></option>
					<option value="1"<?php if( esc_attr( $item->gantrymenu_children_group ) == 1 ) : ?> selected="selected"<?php endif;?>><?php _e( 'Yes', 'rt_cygnet_wp_lang' ); ?></option>
				</select>
			</label>
		</p>

		<p class="field-gantrymenu_dropdown_width description description-thin">
			<label for="edit-menu-item-gantrymenu_dropdown_width-<?php echo $item_id; ?>">
				<?php _e( 'Dropdown Width (px)', 'rt_cygnet_wp_lang' ); ?><br />
				<input type="text" id="edit-menu-item-gantrymenu_dropdown_width-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_dropdown_width" name="menu-item-gantrymenu_dropdown_width[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->gantrymenu_dropdown_width ); ?>"/>
			</label>
		</p>

		<p class="field-gantrymenu_column_widths description description-thin">
			<label for="edit-menu-item-gantrymenu_column_widths-<?php echo $item_id; ?>">
				<?php _e( 'Column Widths (px)', 'rt_cygnet_wp_lang' ); ?><br/>
				<input type="text" id="edit-menu-item-gantrymenu_column_widths-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_column_widths" name="menu-item-gantrymenu_column_widths[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->gantrymenu_column_widths ); ?>"/>
			</label>
		</p>

		<p class="field-gantrymenu_large_direction description description-thin">
			<label for="edit-menu-item-gantrymenu_large_direction-<?php echo $item_id; ?>">
				<?php _e( 'Dropdown on Large', 'rt_cygnet_wp_lang' ); ?><br />
				<select id="edit-menu-item-gantrymenu_large_direction-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_large_direction" name="menu-item-gantrymenu_large_direction[<?php echo $item_id; ?>]">
					<option value="rt-dropdown-left-large"<?php if( esc_attr( $item->gantrymenu_large_direction ) == 'rt-dropdown-left-large' ) : ?> selected="selected"<?php endif;?>><?php _e( 'Left Direction', 'rt_cygnet_wp_lang' ); ?></option>
					<option value="rt-dropdown-right-large"<?php if( esc_attr( $item->gantrymenu_large_direction ) == 'rt-dropdown-right-large' || esc_attr( $item->gantrymenu_large_direction ) == '' ) : ?> selected="selected"<?php endif;?>><?php _e( 'Right Direction', 'rt_cygnet_wp_lang' ); ?></option>
				</select>
			</label>
		</p>

		<p class="field-gantrymenu_desktop_direction description description-thin">
			<label for="edit-menu-item-gantrymenu_desktop_direction-<?php echo $item_id; ?>">
				<?php _e( 'Dropdown on Desktop', 'rt_cygnet_wp_lang' ); ?><br />
				<select id="edit-menu-item-gantrymenu_desktop_direction-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_desktop_direction" name="menu-item-gantrymenu_desktop_direction[<?php echo $item_id; ?>]">
					<option value="rt-dropdown-left-desktop"<?php if( esc_attr( $item->gantrymenu_desktop_direction ) == 'rt-dropdown-left-desktop' ) : ?> selected="selected"<?php endif;?>><?php _e( 'Left Direction', 'rt_cygnet_wp_lang' ); ?></option>
					<option value="rt-dropdown-right-desktop"<?php if( esc_attr( $item->gantrymenu_desktop_direction ) == 'rt-dropdown-right-desktop' || esc_attr( $item->gantrymenu_desktop_direction ) == '' ) : ?> selected="selected"<?php endif;?>><?php _e( 'Right Direction', 'rt_cygnet_wp_lang' ); ?></option>
				</select>
			</label>
		</p>

		<p class="field-gantrymenu_tablet_direction description description-thin">
			<label for="edit-menu-item-gantrymenu_tablet_direction-<?php echo $item_id; ?>">
				<?php _e( 'Dropdown on Tablet', 'rt_cygnet_wp_lang' ); ?><br />
				<select id="edit-menu-item-gantrymenu_tablet_direction-<?php echo $item_id; ?>" class="widefat edit-menu-item-gantrymenu_tablet_direction" name="menu-item-gantrymenu_tablet_direction[<?php echo $item_id; ?>]">
					<option value="rt-dropdown-left-tablet"<?php if( esc_attr( $item->gantrymenu_tablet_direction ) == 'rt-dropdown-left-tablet' ) : ?> selected="selected"<?php endif;?>><?php _e( 'Left Direction', 'rt_cygnet_wp_lang' ); ?></option>
					<option value="rt-dropdown-right-tablet"<?php if( esc_attr( $item->gantrymenu_tablet_direction ) == 'rt-dropdown-right-tablet' || esc_attr( $item->gantrymenu_tablet_direction ) == '' ) : ?> selected="selected"<?php endif;?>><?php _e( 'Right Direction', 'rt_cygnet_wp_lang' ); ?></option>
				</select>
			</label>
		</p>

		<script type="text/javascript">
			((function(){
			var evenly = document.id('gantrymenu_distributioneven-<?php echo $item_id; ?>'),
				inorder = document.id('gantrymenu_distributionorder-<?php echo $item_id; ?>'),
				manually = document.id('gantrymenu_distributionmanual-<?php echo $item_id; ?>'),
				field = document.id('edit-menu-item-gantrymenu_manual_distribution-<?php echo $item_id; ?>');

			$$(evenly, inorder, manually).addEvent('click', function(){
				var isManual = this.id.contains('gantrymenu_distributionmanual');
				if (!isManual) field.getParent('p').setStyle('display', 'none');
				else field.getParent('p').setStyle('display', 'block');
			}).filter(function(input){ return input.get('checked'); }).fireEvent('click');

			})());
		</script>

		<?php
		echo ob_get_clean();
	}
}
