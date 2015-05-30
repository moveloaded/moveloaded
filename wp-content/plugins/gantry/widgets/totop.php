<?php
/**
 * @version   $Id: totop.php 61343 2015-02-25 13:31:01Z jakub $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrywidget');

/**
 * @package     gantry
 * @subpackage  features
 */
add_action('widgets_init', array("GantryWidgetToTop", "init"));


class GantryWidgetToTop extends GantryWidget {
	var $short_name = 'totop';
	var $wp_name = 'gantry_totop';
	var $long_name = 'Gantry To Top';
	var $description = 'Gantry To Top Widget';
	var $css_classname = 'widget_gantry_totop';
	var $width = 200;
	var $height = 400;

	static function init() {
		register_widget("GantryWidgetToTop");
	}

	function render_widget_open($args, $instance) {
		?>
		<div class="clear"></div>
		<?php
		parent::render_widget_open($args, $instance);
	}

	function render($args, $instance) {
		/** @global $gantry Gantry */
        global $gantry;

		$gantry->addScript('mootools.js');
		$gantry->addScript('gantry-totop.js');

		ob_start();
		?>
		<a href="#" id="gantry-totop"><?php echo $instance['text']; ?></a>
		<?php
		echo ob_get_clean();
	}
}