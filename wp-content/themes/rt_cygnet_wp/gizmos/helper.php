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
class GantryGizmoHelper extends GantryGizmo {

	var $_name = 'helper';

	function isEnabled() {
		return true;
	}

	function admin_init() {
		global $gantry;

		/**
		 * Gantry is fully supporting the WordPress menu system via the Gantry Menu widget or Custom Menu widget
		 */
		
		add_theme_support( 'menus' );

		// unregister Gantry deprecated widgets
		add_action( 'widgets_init', array( &$this, 'unregister_deprecated_widgets' ) );
	}

	function query_parsed_init() {
		global $gantry;

		// modify the default Admin Bar margins to render properly in the mobile mode
		add_theme_support( 'admin-bar', array( 'callback' => array( &$this, 'modify_admin_bar_margins' ) ) );

		// add extra CSS classes to get_calendar
		add_filter( 'get_calendar', array( &$this, 'get_calendar_styling' ) );

		// add extra CSS classes to the comments navigation
		add_filter( 'next_comments_link_attributes', array( &$this, 'add_comments_navigation_classes' ) );
		add_filter( 'previous_comments_link_attributes', array( &$this, 'add_comments_navigation_classes' ) );

		// add extra CSS classes to the comments 'Reply' button
		add_filter( 'comment_reply_link', array( &$this, 'add_comments_reply_link_classes' ) );

		// style changes for the password protected posts
		add_filter( 'the_password_form', array( &$this, 'password_form_styling' ) );

		// allow html in widget title
		add_filter( 'widget_title', array( &$this, 'html_widget_title' ) );
	}

	/* Unregister Gantry deprecated widgets */
	function unregister_deprecated_widgets() {
		unregister_widget( 'GantryWidgetiPhoneMenu' );
		unregister_widget( 'GantryWidgetViewSwitcher' );
		unregister_widget( 'GantryWidgetLinks' );
	}

	/* Add extra CSS classes to getcalendar() */
	function get_calendar_styling( $calendar_output ) {
		$calendar_output = str_replace( 'wp-calendar"', 'wp-calendar" class="table table-bordered"', $calendar_output );
		return $calendar_output;
	}

	/* Add extra CSS classes to the comments navigation */
	function add_comments_navigation_classes() {
		$classes = 'class="btn btn-small"';
		return $classes;
	}

	/* Add extra CSS classes to the comments 'Reply' button */
	function add_comments_reply_link_classes( $link ) {
		$link = str_replace( "class='", "class='button ", $link);
		return $link;
	}

	/* style changes for the password protected posts */
	function password_form_styling($output) {
		$output = str_replace( '<input type="submit"', '<input type="submit" class="button"', $output );
		return $output;
	}

	/* allow html in widget title */
	function html_widget_title($title) {
		$title = str_replace( array( '&quot;', '&#8221;' ), '"', $title );
		$title = do_shortcode( $title );

		//convert square brackets to angle brackets
		$title = str_replace( '[', '<', $title );
		$title = str_replace( ']', '>', $title );

		//strip tags other than the allowed set
		$title = strip_tags( $title, '<span>' );
		return $title;
	}

	/* modify the default Admin Bar margins to render properly in the mobile mode */
	function modify_admin_bar_margins() { ?>
		<style type="text/css" media="screen">
			html { margin-top: 32px !important;}
			* html body { margin-top: 32px !important; }
			@media screen and ( max-width: 782px ) {
				html { margin-top: 0 !important; }
				html body #rt-header-surround { margin-top: 46px; }
				html body.scrolling-enable { margin-top: 0; }
				html body.admin-bar.scrolling-fixed-header-disable .gf-menu-toggle { top: 56px !important;}
				html body.admin-bar #wpadminbar {z-index: 30005 !important;}
			}
			/* Admin Bar is absolute so we need to position our header at margin-top 0 */
			@media screen and ( max-width: 600px ) {
				html body.scrolling-enable #rt-header-surround { margin-top: 0; }
				html body.scrolling-enable .gf-menu-toggle { top: 10px !important; }
			}
		</style>
	<?php
	}
}