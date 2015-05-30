<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined( 'GANTRY_VERSION' ) or die();

gantry_import( 'core.gantrywidget' );

class GantryWidgetLogo extends GantryWidget {
	var $short_name = 'logo';
	var $wp_name = 'gantry_logo';
	var $long_name = 'Gantry Logo';
	var $description = 'Gantry Logo Widget';
	var $css_classname = 'widget_gantry_logo';
	var $width = 200;
	var $height = 400;

	function init() {
		register_widget( 'GantryWidgetLogo' );
	}

	function render_widget_open( $args, $instance ) {
	}
	
	function render_widget_close( $args, $instance ) {
	}
	
	function pre_render( $args, $instance ) {
	}
	
	function post_render( $args, $instance ) {
	}

	function render( $args, $instance ) {
		global $gantry;
		
		ob_start();
		?>
		<div id="<?php echo $this->id; ?>" class="rt-logo-block <?php echo $this->css_classname; ?> widget">
			<a href="<?php echo home_url(); ?>" id="rt-logo"></a>
		</div>
		<?php
		echo ob_get_clean();
	}
}
