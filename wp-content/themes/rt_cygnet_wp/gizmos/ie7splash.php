<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrygizmo');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoIE7Splash extends GantryGizmo {

    var $_name = 'ie7splash';

	function isEnabled() {
	    if ($this->get('enabled')) {
	    	return true;
	    }
    }

    function init() {
        
        global $gantry;
        
        $request = basename($_SERVER['REQUEST_URI']);
		
		if ($request != 'unsupported.php' && $gantry->browser->name == 'ie' && $gantry->browser->shortversion == '7') { 
			add_filter('template_include', array(&$this, 'ie7splash_redirect'));     
        }

    }
    
    static function ie7splash_redirect($template) {
    	return locate_template(array('unsupported.php'));
    }
    
}