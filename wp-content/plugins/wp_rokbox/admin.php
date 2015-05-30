<?php
/**
 * @version   2.50.5 November 20, 2014
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * No Direct Access
 */
defined( 'ABSPATH' ) or die( 'Restricted access' );

$option = get_option( 'rokbox2_options' );

if( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true') {
	echo '<div id="message" class="updated fade"><p>' . __('RokBox settings saved.', 'rokbox') . '</p></div>';
}

?>
	<div id="rb-main">
		<form method="post" action="options.php">
			<?php settings_fields( 'rokbox2_options_array' ); ?>

			<table class="widefat fixed">
				<tfoot>
					<tr>
						<th colspan="2">
							<input type="submit" class="button button-primary rb-submit" value="<?php _e('Save Changes', 'rokbox'); ?>" />
						</th>
					</tr>
				</tfoot>
				<tbody>
					<tr>
						<td class="leftcol">
							<h3 class="available-options"><?php _e( 'Available Options', 'rokbox' ); ?></h3>
							<div class="param-row alternate first">
								<div class="label"><?php _e( 'Backwards Compatibility', 'rokbox' ); ?></div>
								<div class="option">
									<input id="backwards_compat1" type="radio" <?php checked( $option['backwards_compat'], '1' ); ?> value="1" name="rokbox2_options[backwards_compat]"/>
									<label for="backwards_compat1"><?php _e('Enable', 'rokbox'); ?></label>&nbsp;&nbsp;
									<input id="backwards_compat2" class="second" type="radio" <?php checked( $option['backwards_compat'], '0' ); ?> value="0" name="rokbox2_options[backwards_compat]"/>
									<label for="backwards_compat2"><?php _e('Disable', 'rokbox'); ?></label>
								</div>
							</div>
							<div class="param-row">
								<div class="label"><?php _e( 'Thumbnail Width', 'rokbox' ); ?></div>
								<div class="option">
									<input class="text-input" id="thumb_width" type="text" maxlength="5" name="rokbox2_options[thumb_width]" value="<?php echo $option['thumb_width']; ?>" placeholder="<?php _e('width in pixels', 'rokbox'); ?>" />
									<span class="add-on">px</span>
								</div>
							</div>
							<div class="param-row alternate">
								<div class="label"><?php _e( 'Thumbnail Height', 'rokbox' ); ?></div>
								<div class="option">
									<input class="text-input" id="thumb_height" type="text" maxlength="5" name="rokbox2_options[thumb_height]" value="<?php echo $option['thumb_height']; ?>" placeholder="<?php _e('height in pixels', 'rokbox'); ?>" />
									<span class="add-on">px</span>
								</div>
							</div>
							<div class="param-row">
								<div class="label"><?php _e( 'Thumbnail Quality', 'rokbox' ); ?></div>
								<div class="option">
									<input class="text-input" id="thumb_quality" type="text" maxlength="3" name="rokbox2_options[thumb_quality]" value="<?php echo $option['thumb_quality']; ?>" />
								</div>
							</div>
							<div class="param-row alternate">
								<div class="label"><?php _e( 'Load Scripts in Footer', 'rokbox' ); ?></div>
								<div class="option">
									<input id="scripts_in_footer1" type="radio" <?php checked( $option['scripts_in_footer'], '1' ); ?> value="1" name="rokbox2_options[scripts_in_footer]"/>
									<label for="scripts_in_footer1"><?php _e('Enable', 'rokbox'); ?></label>&nbsp;&nbsp;
									<input id="scripts_in_footer2" class="second" type="radio" <?php checked( $option['scripts_in_footer'], '0' ); ?> value="0" name="rokbox2_options[scripts_in_footer]"/>
									<label for="scripts_in_footer2"><?php _e('Disable', 'rokbox'); ?></label>
								</div>
							</div>
							<input id="viewport_pc" type="hidden" name="rokbox2_options[viewport_pc]" value="<?php echo $option['viewport_pc']; ?>" />
							<div class="clear"></div>
						</td>
						<td class="rightcol">
							<div class="rb-logo">
								<a href="http://rockettheme.com" target="_blank" id="rokbox" title="RocketTheme LLC."></a>
							</div>
							<div class="branding">
								<h1>RokBox 2.50.5</h1>
							</div>
							<div class="desc">
								<p class="description">
									<strong>RokBox 2.50.5</strong> <?php _e( 'is a fully responsive modal plug-in for WordPress. Rewritten from the ground up it can display many different media formats such as images, videos, music, embedded widgets, Ajax content and WordPress widgets and takes advantage of the new technologies such as HTML5 and CSS3.', 'rokbox' ); ?>
								</p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>