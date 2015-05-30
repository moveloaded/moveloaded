<?php
/**
 * @version   $Id: bbpress.php 60800 2014-05-07 13:08:13Z jakub $
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
class GantryGizmobbPress extends GantryGizmo {

	var $_name = 'bbpress';

	function isEnabled() {
		if( class_exists( 'bbPress' ) ) {
			return true;
		}
	}

	/**
	 *     Copyright (C) 2014 Jakub Baran
	 */

	function init() {
		/** @global $gantry Gantry */
		global $gantry;

		/**
		 * bbPress - add extra content location paths
		 */

		add_action( 'after_setup_theme', array( &$this, 'bbpress_add_content_location' ) );

	}

	function query_parsed_init() {
		/** @global $gantry Gantry */
		global $gantry;

		/**
		 * bbPress Compatibility - include bbPress content into mainbody
		 */

		if( is_bbpress() ) {
			add_filter( 'gantry_mainbody_include', array( &$this, 'bbpress_mainbody_include' ) );
		}

	}

	/**
	 * bbPress - adds extra bbPress content locations to the Gantry
	 */

	function bbpress_add_content_location() {
		global $gantry;

		$template_locations = bbp_get_template_stack();
		$template_locations = array_reverse( $template_locations );

		if( ( $key = array_search( $gantry->templatePath, $template_locations ) ) !== false ) {
			unset( $template_locations[$key] );
		}

		foreach( $template_locations as $location ) {
			$gantry->addContentTypePath( $location );
		}
	}

	/**
	 * bbPress - include bbPress content into mainbody
	 */

	function bbpress_mainbody_include( $tmpl ) {
		global $gantry;

		if( is_bbpress() ) {
			foreach( array( 'plugin-bbpress.php', 'bbpress.php', 'forums.php', 'forum.php', 'generic.php', 'page.php' ) as $template ) {
				foreach( $gantry->_contentTypePaths as $file_path ) {
					if( file_exists( $file_path . '/' . $template ) ) return $file_path . '/' . $template;
				}
			}
		}

		return $tmpl;
	}

}