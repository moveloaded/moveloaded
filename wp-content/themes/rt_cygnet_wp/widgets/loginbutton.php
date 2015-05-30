<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
 
defined('GANTRY_VERSION') or die();

gantry_import('core.gantrywidget');

add_action('widgets_init', array("GantryWidgetLoginButton","init"));

class GantryWidgetLoginButton extends GantryWidget {
	var $short_name = 'loginbutton';
	var $wp_name = 'gantry_loginbutton';
	var $long_name = 'Gantry Login Button';
	var $description = 'Gantry Login Button Widget';
	var $css_classname = 'widget_gantry_loginbutton';
	var $width = 200;
	var $height = 400;

	function init() {
		register_widget("GantryWidgetLoginButton");
	}
	
	function render_widget_open($args, $instance) {
	}
	
	function render_widget_close($args, $instance) {
	}
	
	function pre_render($args, $instance) {
	}
	
	function post_render($args, $instance) {
	}
	
	function render_title($args, $instance) {
		global $gantry;
		if($instance['title'] != '') :
			echo apply_filters( 'widget_title', $instance['title'], $instance );
		endif;
	}

	function render($args, $instance){
		global $gantry, $current_user;
		ob_start();
		?>
		
		<div id="<?php echo $this->id; ?>" class="rt-block rt-popupmodule-button <?php echo $this->css_classname; ?> widget">
			<?php if(!is_user_logged_in()) : ?>
				<?php echo $this->_renderRokBoxLink(); ?>
					<span class="desc"><?php echo $instance['logintext']; ?></span>
				</a>
			<?php else : ?>
				<a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>" class="buttontext button">
					<span class="desc"><?php echo $instance['logouttext'] . ' ' . $current_user->display_name; ?></span>
				</a>
			<?php endif; ?>
		</div>
		
		<?php 
		
		echo ob_get_clean();
	
	}

	function _renderRokBoxLink() {
		$isRokBox2 = @file_exists( WP_PLUGIN_DIR . '/wp_rokbox/tinymce/tinymce.php' );
		$output = array();

		if ( $isRokBox2 ) {
			$output[] = '<a data-rokbox data-rokbox-element="#rt-popuplogin" href="#" class="buttontext button">';
		} else {
			$output[] = '<a href="#" class="buttontext button" rel="rokbox[385 200][module=rt-popuplogin]">';
		}

		return implode( "\n", $output );
	}
}