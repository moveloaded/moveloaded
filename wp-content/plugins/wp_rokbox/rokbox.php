<?php
/**
 * @version   2.50.5 November 20, 2014
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
/*
Plugin Name: RokBox
Plugin URI: http://www.rockettheme.com
Description: RokBox 2.50.5 is a fully responsive modal plug-in for WordPress and the successor of RokBox1. Rewritten from the ground up it can display many different media formats such as images, videos, music, embedded widgets, Ajax content and WordPress widgets and takes advantage of the new technologies such as HTML5 and CSS3.
Author: RocketTheme, LLC
Version: 2.50.5
Author URI: http://www.rockettheme.com
Text Domain: rokbox
Domain Path: /languages
License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
Uses widget_content filter from Widget Logic plugin by Alan Trewartha
*/

/**
 * No Direct Access
 */
defined( 'ABSPATH' ) or die( 'Restricted access' );

/**
 * RokBox Class
 */
class RokBox {

	/**
	 * @var string
	 */
	protected $_version = '2.50.5';

	/**
	 * @var string
	 */
	protected $_basepath;

	/**
	 * @var string
	 */
	protected $_url;

	/**
	 * @var string
	 */
	protected $_domain = 'rokbox';

	/**
	 * @var bool
	 */
	protected static $_assetsLoad = false;

	/**
	 * Object init
	 */
	function __construct() {
		global $plugin, $network_plugin;

		/**
		 * Set the $_basepath and $_url variables
		 */
		if( !is_multisite() ) {
			$this->_basepath = dirname( $plugin );
		} else {
			if( !empty( $network_plugin ) ) {
				$this->_basepath = dirname( $network_plugin );
			} else {
				$this->_basepath = dirname( $plugin );
			}
		}

		$this->_url = untrailingslashit( plugin_dir_url( basename( $this->_basepath ) . '/rokbox.php' ) );

		$this->_register_hooks();
	}

	function __get( $var ) {
		return $this->$var;
	}

	/**
	 * Register RokBox hooks
	 */
	function _register_hooks() {
		global $plugin, $network_plugin;

		/**
		 * MooTools Init
		 */
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_mootools' ), -50 );

		/**
		 * Set default options
		 */
		register_activation_hook( $plugin, array( &$this, 'default_options' ) );

		/**
		 * Check if options exist
		 */

		$options_exist = get_option( 'rokbox2_options' );
		if( !$options_exist )
			add_action( 'admin_init', array( &$this, 'default_options' ) );

		/**
		 * Load plugin translations
		 */
		add_action( 'plugins_loaded', array( &$this, 'load_translation' ) );

		/**
		 * Load frontend assets
		 */
		if ( !self::$_assetsLoad ) {
			/**
			 * Enqueue the frontend CSS
			 */
			add_action( 'wp_enqueue_scripts', array( &$this, 'compileLESS' ) );

			/**
			 * Enqueue the frontend JavaScript
			 */
			add_action( 'wp_enqueue_scripts', array( &$this, 'compileJS' ) );

			/**
			 * Add javascript declaration
			 */
			add_action( 'wp_head', array( &$this, 'script_declaration' ) );

			self::$_assetsLoad = true;
		}

		/**
		 * CALLED ON 'dynamic_sidebar_params' FILTER - this is called during 'dynamic_sidebar' just before each callback is run
		 * swap out the original callback and replace it with our own
		 */
		add_filter( 'dynamic_sidebar_params', array( &$this, 'widget_display_callback' ), 11 );

		/**
		 * Backwards compatibility syntax replacement
		 */
		add_filter( 'the_content', array( &$this, 'backwards_compatibility' ), 99998 );
		add_filter( 'widget_content', array( &$this, 'backwards_compatibility' ), 99998 );

		/**
		 * RokBox content prepare
		 */
		add_filter( 'the_content', array( &$this, 'content_prepare' ), 99999 );
		add_filter( 'widget_content', array( &$this, 'content_prepare' ), 99999 );

		// Admin Page

		/**
		 * Add the TinyMCE RokBox plugin
		 */
		add_action( 'plugins_loaded', array( &$this, 'add_tinymce_plugin' ) );

		/**
		 * Modify the TinyMCE options
		 */
		add_filter( 'tiny_mce_before_init', array( &$this, 'modify_tinymce_options' ) );

		/**
		 * Add RokBox settings page menu item
		 */
		add_action( 'admin_menu', array( &$this, 'admin_item' ) );

		/**
		 * Register RokBox settings in the admin
		 */
		add_action( 'admin_init', array( &$this, 'register_admin_settings' ) );

		/**
		 * Enqueue settings page scripts
		 */
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Enqueue MooTools
	 */
	function enqueue_mootools() {
		$options = get_option( 'rokbox2_options' );
		( $options['scripts_in_footer'] == '0' || $options['scripts_in_footer'] === null ) ? $scripts_in_footer = false : $scripts_in_footer = true;

		if( wp_script_is( 'rok_mootools_js' ) == false ) {
			wp_register_script( 'mootools.js', $this->_url . '/assets/js/mootools.js', array(), '1.4.5', $scripts_in_footer );
			wp_enqueue_script( 'mootools.js' );
		}
	}

	/**
	 * Method to load the plugin translations
	 */
	function load_translation() {
		$domain = $this->_domain;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/wp_rokbox/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( $this->_basepath ) . '/languages/' );
	}

	/**
	 * RokBox 2 default options
	 */
	function default_options() {
		/**
		 * Set default options
		 */
		$defaults = array(
			'viewport_pc' => '100',
			'backwards_compat' => '0',
			'thumb_width' => '150',
			'thumb_height' => '100',
			'thumb_quality' => '90',
			'scripts_in_footer' => '0'
		);

		add_option( 'rokbox2_options', $defaults );
	}

	/**
	 * Add the script declaration to the frontend <head>
	 */
	function script_declaration() {
		$options = get_option( 'rokbox2_options' );
		$script = '<script type="text/javascript">' . "if (typeof RokBoxSettings == 'undefined') RokBoxSettings = {pc: '" . $options['viewport_pc'] . "'};" . '</script>' . "\n";
		echo $script;
	}

	/**
	 * Compile LESS files
	 */
	function compileLess() {
		$assets = $this->_basepath . '/assets';

		if( ( defined( 'ROKBOX_DEV' ) && ROKBOX_DEV ) || @file_exists( $assets . '/less/lessc.inc.php' ) ) {
			//define("DEV", true); // to recompile/unify JS
			@include_once( $assets . '/less/lessc.inc.php' );
			try {
				$css_file = $assets . '/styles/rokbox.css';
				@unlink( $css_file );
				lessc::ccompile( $assets . '/less/global.less', $css_file );
			} catch ( exception $e ) {
				echo 'LESS Compiler: ' . $e->getMessage();
			}
		}

		wp_enqueue_style( 'rokbox.css', $this->_url . '/assets/styles/rokbox.css', array(), $this->_version );
	}

	/**
	 * Compile JS files
	 */
	function compileJS() {
		$options = get_option( 'rokbox2_options' );

		( $options['scripts_in_footer'] == '0' || $options['scripts_in_footer'] === null ) ? $scripts_in_footer = false : $scripts_in_footer = true;

		$assets   = $this->_basepath . '/assets';

		if ( ( defined( 'ROKBOX_DEV' ) && ROKBOX_DEV ) || @file_exists( $assets . '/less/lessc.inc.php' ) ) {
			$buffer = "";
			$assets = $this->_basepath . '/assets';

			$app    = $assets . '/application/';
			$output = $assets . '/js/';

			$files = array(
				$app . 'moofx',
				$app . 'mootools-mobile',
				$app . 'RokBox.Media',
				$app . 'RokBox'
			);

			foreach ($files as $file) {
				$file    = $file . '.js';
				$content = false;

				if ( file_exists( $file ) ) $content = file_get_contents( $file );

				$buffer .= ( !$content ) ? "\n\n !!! File not Found: " . $file . " !!! \n\n" : $content;
			}

			file_put_contents( $output . 'rokbox.js', $buffer );
		}

		if( wp_script_is( 'rok_mootools_js' ) ) {
			wp_enqueue_script( 'rokbox.js', $this->_url . '/assets/js/rokbox.js', array( 'rok_mootools_js' ), $this->_version, $scripts_in_footer );
		} else {
			wp_enqueue_script( 'rokbox.js', $this->_url . '/assets/js/rokbox.js', array( 'mootools.js' ), $this->_version, $scripts_in_footer );
		}
	}

	/**
	 * Changes the widget callback function
	 * @param $params
	 * @return mixed
	 */
	function widget_display_callback($params) {
		global $wp_registered_widgets;

		$id = $params[0]['widget_id'];
		$wp_registered_widgets[$id]['callback_redirect'] = $wp_registered_widgets[$id]['callback'];
		$wp_registered_widgets[$id]['callback'] = array( &$this, 'widget_redirected_callback' );

		return $params;
	}

	/**
	 * Widget redirected callback allows us to filter out the widgets content
	 */
	function widget_redirected_callback() {
		global $wp_registered_widgets, $wp_reset_query_is_done;

		// replace the original callback data
		$params = func_get_args();
		$id = $params[0]['widget_id'];
		$callback = $wp_registered_widgets[$id]['callback_redirect'];
		$wp_registered_widgets[$id]['callback'] = $callback;

		// run the callback but capture and filter the output using PHP output buffering
		if ( is_callable( $callback ) ) {
			ob_start();
			call_user_func_array( $callback, $params );
			$widget_content = ob_get_contents();
			ob_end_clean();
			echo apply_filters( 'widget_content', $widget_content, $id );
		}
	}

	function backwards_compatibility( $content ) {
		$options = get_option( 'rokbox2_options' );

		if ( !$options['backwards_compat'] ) return $content;

		if( strpos( $content, 'rel="rokbox' ) !== false ) {
			preg_match_all( '#<a(.*?)(rel="rokbox.*?)>(.*?)</a>#', $content, $matches );
			foreach( $matches[0] as $element ) {
				$orig_element = $element;

				preg_match( '/title="(.*?)"/', $element, $title );
				$title = isset( $title[1] ) ? $title[1] : false;

				preg_match( '/rel="rokbox(.*?)"/', $element, $rel );
				$rel = isset( $rel[1] ) ? $rel[1] : false;

				preg_match( "/\((.+)\)/", $rel, $album );
				$album = isset( $album[1] ) ? $album[1] : false;

				preg_match( "/\[module=(.+)\]/", $rel, $module );
				$module = isset( $module[1] ) ? '#' . $module[1] : false;

				preg_match( "/\[(\d{1,})\s+(\d{1,})\]/", $rel, $size );
				$size = isset( $size[1] ) && isset( $size[2] ) ? $size[1] . ' ' . $size[2] : false;

				if ( strlen( $title ) ) {
					@list( $title, $caption ) = explode( ' :: ', $title );
					$caption = '&lt;h4&gt;' . $title . '&lt;/h4&gt;' . ( isset( $caption ) && strlen( $caption ) ? '&lt;span&gt;' . $caption . '&lt;/span&gt;' : '' );
				}

				$element = preg_replace( '/rel="rokbox(.*?)"/', '', $element );
				$rokbox_attr = ' data-rokbox=""';

				if ( $title ) $rokbox_attr .= ' data-rokbox-caption="' . $caption . '"';
				if ( $album ) $rokbox_attr .= ' data-rokbox-album="' . $album . '"';
				if ( $module ) $rokbox_attr .= ' data-rokbox-element="' . $module . '"';
				if ( $size ) $rokbox_attr .= ' data-rokbox-size="' . $size . '"';

				$element = preg_replace( '#<a(.*?)>(.*?)</a>#', '<a$1' . $rokbox_attr . '>$2</a>', $element );

				$content = str_replace( $orig_element, $element, $content );
			}
		}

		return $content;
	}

	function content_prepare( $content ) {
		$options = get_option( 'rokbox2_options' );

		// simple performance check to determine whether bot should process further
		if( strpos( $content, 'rokbox' ) === false ) return $content;

		// define the regular expression for the bot
		$regex = "#\[rokbox(.*?)\](.*?)\[/rokbox\]#smi";
		$thumb_generator = "#data-rokbox-generate-thumbnail#smi";

		preg_match_all( $thumb_generator, $content, $matches );
		$count = count( $matches[0] );
		if ( $count ){
			$this->generate_thumbnails( $content, $matches, $count, $thumb_generator, $options );
		}

		if( !$options['backwards_compat'] ) return $content;
		// find all instances of plugin and put in $matches
		preg_match_all( $regex, $content, $matches );

		// Number of plugins
		$count = count( $matches[0] );

		// plugin only processes if there are any instances of the plugin in the text
		if ( $count ){
			$this->process_snippets( $content, $matches, $count, $regex, $options );
		}

		return $content;
	}

	function generate_thumbnails(&$row, &$matches, $count, $regex, $botParams) {

		if( strpos( $row, 'data-rokbox-generate-thumbnail' ) !== false ) {
			preg_match_all( '#<a(.*?)(data-rokbox-generate-thumbnail.*?)>(.*?)</a>#', $row, $found );
			foreach( $found[0] as $element ) {
				$orig_element = $element;

				preg_match( '/href="(.*?)"/', $element, $href );
				$href = isset( $href[1] ) ? $href[1] : '';

				if( is_multisite() ) {
					$wpurl = trailingslashit( network_site_url() );
				} else {
					$wpurl = trailingslashit( site_url() );
				}

				if( strpos( $href, $wpurl ) !== false ) {
					$href = str_replace( $wpurl, '', $href );
				}

				if(
					!preg_match( "/\.(jpe?g|png|gif|bmp|tiff?)$/i", $href, $extension ) ||
					!is_file( $href )
				) continue;

				if( substr( $href, -10 ) == '_thumb.jpg' || substr( $href, -10 ) == '-thumb.jpg' ) {
					$element = str_replace( '>', '>' . $href, $element );
					$row = str_replace( $orig_element, $element, $row );
					continue;
				}

				$extension = $extension[0];
				$basename = substr( $href, 0, -( strlen( $extension ) ) );
				$input = ABSPATH  . $basename . $extension;
				$output = ABSPATH  . $basename . '_thumb.jpg';

				if ( @file_exists( $output ) ){
					$imageSize = @getimagesize( $input );
					$element = preg_replace( '#<a(.*?)(data-rokbox-generate-thumbnail.*?)>(.*?)</a>#', '<a$1$2><img class="rokbox-thumb" src="' . $wpurl . $basename . '_thumb.jpg" style="max-width:' . $imageSize[0] . 'px;max-height:' . $imageSize[1] . 'px;" /></a>', $element );
					$row = str_replace( $orig_element, $element, $row );
					continue;
				}

				@require_once( $this->_basepath . '/lib/imagehandler.php' );
				$sizeSettings = array(
					'width' => $botParams['thumb_width'],
					'height' => $botParams['thumb_height']
				);
				$imageSize = @getimagesize( $input );

				if( $imageSize[0] < $sizeSettings['width'] ) $sizeSettings['width'] = $imageSize[0];
				if( $imageSize[1] < $sizeSettings['height'] ) $sizeSettings['height'] = $imageSize[1];

				$thumb = new imgRedim( false, false, WP_CONTENT_DIR );
				$thumb->loadImage( $input );
				$thumb->redimToSize( $sizeSettings['width'], $sizeSettings['height'], true );
				$thumb->saveImage( $output, $botParams['thumb_quality'] );

				$element = preg_replace( '#<a(.*?)(data-rokbox-generate-thumbnail.*?)>(.*?)</a>#', '<a$1$2><img class="rokbox-thumb" src="' . $wpurl . $basename . '_thumb.jpg" style="max-width:' . $sizeSettings['width'] . 'px;max-height:' . $sizeSettings['height'] . 'px;" /></a>', $element );
				$row = str_replace( $orig_element, $element, $row );
			}
		}

		return $row;
	}

	function process_snippets( &$row, &$matches, $count, $regex, $botParams ){
		$snippets = $matches[0];
		$snippets_settings = $matches[1];
		$snippets_links = $matches[2];

		$text = $row;

		foreach( $snippets as $index => $snippet ) {
			$settings = isset( $snippets_settings[$index] ) ? trim( $snippets_settings[$index] ) : '';
			$href = isset( $snippets_links[$index] ) ? trim( $snippets_links[$index] ) : '#';

			$prefix = 'data-rokbox-';
			$content = '';
			$options = $link = array();
			preg_match_all( "#([a-z]{1,})=\"(.*?)\"#si", $settings, $data );

			if( is_multisite() ) {
				$wpurl = trailingslashit( network_site_url() );
			} else {
				$wpurl = trailingslashit( site_url() );
			}

			if( strpos( $href, $wpurl ) !== false ) {
				$href = str_replace( $wpurl, '', $href );
			}

			// logic for wildcards scan
			$items = @glob( ltrim( $href, '/' ) );
			if( !empty( $items ) ) {
				$i = 0;
				foreach( $items as $item ) {
					if( !preg_match( "/\.(jpe?g|png|gif|bmp|tiff?)$/", $item ) ) unset( $items[$i] );
					$i++;
				}
				$items = array_values( $items );
			}

			if( count( $items ) ) {
				if( !array_search( 'thumb', $data[1] ) && count( $items ) > 1) array_push( $data[1], 'thumb' );
				elseif( !array_search( 'thumb', $data[1] ) && !array_search( 'text', $data[1] ) ) array_push( $data[1], 'thumb' );

				if( array_search( 'thumb', $data[1] ) && $needle = array_search( 'text', $data[1] ) ) { unset( $data[0][$needle] ); unset( $data[1][$needle] ); unset( $data[2][$needle] ); }
				$wildcards = array();
			}

			if( !count( $items ) ) {
				if( ( $needle = array_search( 'text', $data[1] ) ) === false ) $content = $data[2][$needle];
				elseif( ( $needle = array_search( 'title', $data[1] ) ) === false ) $content = $data[2][$needle];
				else $content = __( 'Image not found', 'rokbox' );

				if( !array_search( 'thumb', $data[1] ) || ( is_dir( dirname( $href ) ) && !is_file( $href ) ) ) {
					array_push( $items, $href );
				}
			}

			foreach( $data[1] as $key_index => $key ) {
				$value = isset( $data[2][$key_index] ) ? $data[2][$key_index] : null;

				switch( $key ) {
					case 'text':
						$content = $value;
						break;
					case 'thumb': case 'thumbnail':
						if( !count( $items ) || !$value ) array_push( $items, $value );

						foreach( $items as $value ) {
							if(
								!preg_match( "/\.(jpe?g|png|gif|bmp|tiff?)$/", $value, $extension ) ||
								!is_file( $value )
								) break;

							if( substr( $value, -10 ) != '_thumb.jpg' && substr( $value, -10 ) != '-thumb.jpg' ) {
								@require_once( $this->_basepath . '/lib/imagehandler.php' );

								$extension = $extension[0];
								$basename = substr( $value, 0, -( strlen( $extension ) ) );
								$input = ABSPATH  . $basename . $extension;
								$output = ABSPATH  . $basename . '_thumb.jpg';

								$sizeSettings = array(
									'width' => $botParams['thumb_width'],
									'height' => $botParams['thumb_height']
								);
								$imageSize = @getimagesize( $input );

								if( $imageSize[0] < $sizeSettings['width'] ) $sizeSettings['width'] = $imageSize[0];
								if( $imageSize[1] < $sizeSettings['height'] ) $sizeSettings['height'] = $imageSize[1];

								$thumb = new imgRedim( false, false, WP_CONTENT_DIR );
								$thumb->loadImage( $input );
								$thumb->redimToSize( $sizeSettings['width'], $sizeSettings['height'], true );
								$thumb->saveImage( $output, $botParams['thumb_quality'] );

								$img = '<img class="rokbox-thumb" src="' . $wpurl . $basename . '_thumb.jpg" style="max-width:' . $sizeSettings['width'] . 'px;max-height:' . $sizeSettings['height'] . 'px;" />';
							} else {
								$imageSize = @getimagesize( $value );
								$img = '<img class="rokbox-thumb" src="' . $wpurl . $value . '" style="max-width:' . $imageSize[0] . 'px;max-height:' . $imageSize[1] . 'px;" />';
							}

							if( count( $items ) == 1 ) $content = $img;
							else $wildcards[$value] = $img;
						}
						break;
					default;
						if( $key == 'title' ) $key = 'caption';
						$value = htmlentities( $value, ENT_QUOTES );
						array_push( $link, $prefix . $key . '="' . $value . '"' );
						break;
				}

			}

			$dataset = implode( ' ', $link );

			if( count( $items ) == 1 ) {
				$link = '<a data-rokbox class="rokbox-link" href="' . $href . '" ' . $dataset . '>' . $content . '</a>';
			} else {
				$link = '';
				if( isset( $wildcards ) && count( $wildcards ) ) {

					if( count( $wildcards ) > 1 ) {
						$link = array(
							'<div class="rokbox-album-wrapper">',
								'<div class="rokbox-album-top">',
									'<div class="rokbox-album-top2">',
										'<div class="rokbox-album-top3"></div>',
									'</div>',
								'</div>',
								'<div class="rokbox-album-inner">',
						);

						foreach( $wildcards as $href => $content ) {
							if( substr( $href, -10 ) != '_thumb.jpg' && substr( $href, -10 ) != '-thumb.jpg' ) {
								array_push( $link, '<a data-rokbox class="rokbox-link" href="' . $href . '" ' . $dataset . '>' . $content . '</a>' );
							}
						}

						array_push( $link, '</div><div class="rokbox-album-bottom"><div class="rokbox-album-bottom2"><div class="rokbox-album-bottom3"></div></div></div></div>' );
					} else {
						$link = array();
						foreach( $wildcards as $href => $content ) {
							array_push( $link, '<a data-rokbox class="rokbox-link" href="' . $href . '" ' . $dataset . '>' . $content . '</a>' );
						}
					}

					$link = implode( "\n", $link );
				}
			}

			$text = str_replace( $snippet, $link, $text );
		}

		$row = $text;
	}

	/////////////////////////////
	/////                   /////
	/////		ADMIN		/////
	/////					/////
	/////////////////////////////

	/**
	 * Add the TinyMCE RokBox plugin
	 */
	function add_tinymce_plugin() {
		if( file_exists( $this->_basepath . '/tinymce/tinymce.php' ) ) {
			require_once( $this->_basepath . '/tinymce/tinymce.php' );
		}
	}

	/**
	 * Modify the TinyMCE options
	 */
	function modify_tinymce_options( $init ) {
		$init['extended_valid_elements'] = '#a[*]';
		return $init;
	}

	/**
	 * Add admin menu item
	 */
	function admin_item() {
		add_plugins_page( 'RokBox Settings', 'RokBox', 'activate_plugins', 'rokbox-settings', array( &$this, 'render_settings' ) );
	}

	/**
	 * Register Settings
	 */
	function register_admin_settings() {
		register_setting( 'rokbox2_options_array', 'rokbox2_options' );
	}

	/**
	 * Enqueues the CSS styling for the settings page
	 */
	function enqueue_admin_scripts() {
		if( isset( $_GET['page'] ) && $_GET['page'] == 'rokbox-settings' ) {
			wp_enqueue_style( 'admin.css', $this->_url . '/assets/admin/css/admin.css' );
		}
	}

	/**
	 * Admin settings form
	 */
	function render_settings() {
		if( file_exists( $this->_basepath . '/admin.php' ) ) {
			require_once( $this->_basepath . '/admin.php' );
		}
	}
}

$rokbox = new RokBox();