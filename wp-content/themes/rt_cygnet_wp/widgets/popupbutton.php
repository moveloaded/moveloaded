<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
 
defined('GANTRY_VERSION') or die();

gantry_import('core.gantrywidget');

add_action('widgets_init', array("GantryWidgetPopupButton","init"));

class GantryWidgetPopupButton extends GantryWidget {
    var $short_name = 'popupbutton';
    var $wp_name = 'gantry_popupbutton';
    var $long_name = 'Gantry Popup Button';
    var $description = 'Gantry Popup Button Widget';
    var $css_classname = 'widget_gantry_popupbutton';
    var $width = 200;
    var $height = 400;

    function init() {
        register_widget("GantryWidgetPopupButton");
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
    }

    function render($args, $instance){
        global $gantry, $current_user;
	    ob_start();
	    ?>
    	
    	<div id="<?php echo $this->id; ?>" class="rt-block rt-popupmodule-button <?php echo $this->css_classname; ?> widget">
			<?php echo $this->_renderRokBoxLink($instance); ?>
				<span class="desc"><?php echo $instance['text']; ?></span>
			</a>
		</div>
    	
	    <?php 
	    
	    echo ob_get_clean();
	
	}

    function _renderRokBoxLink($instance){
        $isRokBox2 = @file_exists(WP_PLUGIN_DIR . '/wp_rokbox/tinymce/tinymce.php');
        $output = array();

        if ($isRokBox2){
            $output[] = '<a class="buttontext button" data-rokbox href="#" data-rokbox-element="#rt-popupmodule">';
        } else {
            $output[] = '<a href="#" class="buttontext button" rel="rokbox[' . $instance['width'] . ' ' . $instance['height'] . '][module=rt-popupmodule]">';
        }

        return implode("\n", $output);
    }
}