<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined( 'GANTRY_VERSION' ) or die();

gantry_import( 'core.gantrygizmo' );

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoTMPLRedirect extends GantryGizmo {

	var $_name = 'tmplredirect';

	function isEnabled() {
		return true;
	}

	function init() {
		global $gantry;
		
		if( isset( $_GET['tmpl'] ) ) {
			$tmpl = $_GET['tmpl'];
			$tmpl = strip_tags( $tmpl );
		}

		$allowed = array(
				'debugbody',
				'unsupported',
				'comingsoon',
				'404'
			);

		if( isset( $tmpl ) && $tmpl != '' && locate_template( array( $tmpl . '.php' ) ) && in_array( $tmpl, $allowed ) ) {
			add_action( 'template_redirect', array( &$this, 'tmpl_redirect' ) );
		}

	}
	
	static function tmpl_redirect() {
		global $gantry;

		$tmpl = $_GET['tmpl'];
		$tmpl = strip_tags( $tmpl );

		include( trailingslashit( $gantry->templatePath ) . $tmpl . '.php' );
		exit();
	}
	
}