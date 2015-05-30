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

class RokBox_TinyMCE {

	/**
	 * @var string
	 */
	var $pluginname = 'RokBox';

	/**
	 * @var string
	 */
	private $_basepath;

	/**
	 * @var string
	 */
	private $_url;

	/**
	 * @var int
	 */
	var $internalVersion = 250;

	/**
	 * Class Constructor
	 */
	function __construct() {
		global $rokbox;

		$this->_basepath = $rokbox->_basepath;
		$this->_url = $rokbox->_url;

		$this->_register_hooks();
	}

	function _register_hooks() {
		add_action( 'init', array( &$this, 'add_buttons' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_mootools' ) );
		add_action( 'wp_ajax_rokbox_tinymce', array( &$this, 'tinymce_ajax' ) );
		add_action( 'wp_ajax_nopriv_rokbox_tinymce', array( &$this, 'tinymce_ajax' ) );
		add_action( 'admin_print_footer_scripts', array( &$this, 'quicktags' ) );
	}

	/**
	 * @return mixed
	 */
	function add_buttons() {
		// Don't bother doing this stuff if the current user lacks permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) )
			return;

		// Add only in Rich Editor mode
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', array( &$this, 'insert_button' ) );
			add_filter( 'mce_buttons', array( &$this, 'register_button' ) );
		}
	}

	// Load the TinyMCE plugin : rokbox.js
	/**
	 * @param $plugin_array
	 * @return array
	 */
	function insert_button( $plugin_array ) {
		$plugin_array['rokbox'] = $this->_url. '/tinymce/editor_plugin.js';
		return $plugin_array;
	}

	/**
	* @param $buttons
	* @return array
	*/
	function register_button( $buttons ) {
		array_push( $buttons, 'separator', 'rokbox' );
		return $buttons;
	}

	/**
	 * Load MooTools on the edit post/page pages
	 */
	function enqueue_mootools() {
		global $pagenow;

		if( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) {
			wp_enqueue_script( 'mootools.js', $this->_url . '/assets/js/mootools.js' );
		}
	}

	/**
	 * Call TinyMCE window content via admin-ajax
	 * there is no iframe handler in WP so we use ajax
	 */
	function tinymce_ajax() {
		ob_start();
		require_once( $this->_basepath . '/tinymce/views/rokbox-picker.php' );
		$content = ob_get_contents();
		ob_end_clean();

		echo $content;

		// IMPORTANT: don't forget to "exit"
		exit;
	}

	/**
	 * Adds RokBox QuickTag Buttons
	 */
	function quicktags() {
		global $wp_version;

		// add thickbox
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'quicktags' );
		add_thickbox();
		?>

		<script type ="text/javascript">
		if( typeof(QTags) == 'function' ) {
			QTags.addButton('rokbox', 'rokbox', '', '');

			// the rokbox button
			window.addEvent('domready', function() {
				if($('qt_content_rokbox')) {
					$('qt_content_rokbox').addEvent('mouseover', function() {
						if($('rokbox_link') == null) {
							var url = ajaxurl + '?action=rokbox_tinymce&type=text&TB_iframe=true&height=675&width=450&modal=false';
							var newLinknew = Element('a#rokbox_link').wraps(this);
							$('rokbox_link').setProperty('href', url);
							$('rokbox_link').setProperty('class', 'thickbox');
						}
					})
				}
			});
		}
		</script>

		<?php
	}
}

new RokBox_TinyMCE();