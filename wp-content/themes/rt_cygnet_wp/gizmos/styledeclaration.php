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
class GantryGizmoStyleDeclaration extends GantryGizmo {

	var $_name = 'styledeclaration';
	
	function isEnabled(){
		global $gantry;
		$menu_enabled = $this->get('enabled');

		if (1 == (int)$menu_enabled) return true;
		return false;
	}

	function query_parsed_init() {
		global $gantry, $ie_ver, $g_platform;
		$browser = $gantry->browser;

		if ($gantry->browser->name == 'ie' && $gantry->browser->shortversion == 8) $ie_ver = 'ie8';
		if ($gantry->browser->name == 'ie' && $gantry->browser->shortversion <= 9) $ie_ver = 'oldIE';
		if ($gantry->browser->platform == 'iphone' || $gantry->browser->platform == 'ipad' || $gantry->browser->platform == 'android') $g_platform = 'mobile';

		// Logo
		$css = $this->buildLogo();

		// Less Variables
		$lessVariables = array(
			'demostyle-type'                => $gantry->get('demostyle-type',               'preset1'),

			'accent-color1'                 => $gantry->get('accent-color1',                '#22EAB8'),
			'accent-color2'                 => $gantry->get('accent-color2',                '#3949A0'),
			'accent-color3'                 => $gantry->get('accent-color3',                '#FFEB3B'),

			'pagesurround-background'       => $gantry->get('pagesurround-background',      '#13112B'),

			'mainbody-overlay'              => $gantry->get('mainbody-overlay',             'dark'),

			'top-textcolor'                 => $gantry->get('top-textcolor',                '#FFFFFF'),
			'top-background'                => $gantry->get('top-background',               '#3949A0'),

			'header-textcolor'              => $gantry->get('header-textcolor',             '#FFFFFF'),
			'header-background'             => $gantry->get('header-background',            '#2D3C8E'),

			'slideshow-textcolor'           => $gantry->get('slideshow-textcolor',          '#FFFFFF'),
			'slideshow-background'          => $gantry->get('slideshow-background',         '#3949A0'),
			'slideshow-type'                => $gantry->get('slideshow-type',               'animation'),

			'showcase-textcolor'            => $gantry->get('showcase-textcolor',           '#FFFFFF'),
			'showcase-background'           => $gantry->get('showcase-background',          '#3949A0'),

			'utility-textcolor'             => $gantry->get('utility-textcolor',            '#777496'),
			'utility-background'            => $gantry->get('utility-background',           '#13112B'),

			'feature-textcolor'             => $gantry->get('feature-textcolor',            '#777496'),
			'feature-background'            => $gantry->get('feature-background',           '#13112B'),

			'maintop-textcolor'             => $gantry->get('maintop-textcolor',            '#FFFFFF'),
			'maintop-background'            => $gantry->get('maintop-background',           '#3949A0'),

			'expandedtop-textcolor'         => $gantry->get('expandedtop-textcolor',        '#8582A5'),
			'expandedtop-background'        => $gantry->get('expandedtop-background',       '#1D1A37'),

			'expandedbottom-textcolor'      => $gantry->get('expandedbottom-textcolor',     '#777496'),
			'expandedbottom-background'     => $gantry->get('expandedbottom-background',    '#13112B'),

			'mainbottom-textcolor'          => $gantry->get('mainbottom-textcolor',         '#777496'),
			'mainbottom-background'         => $gantry->get('mainbottom-background',        '#13112B'),

			'extension-textcolor'           => $gantry->get('extension-textcolor',          '#8582A5'),
			'extension-background'          => $gantry->get('extension-background',         '#1D1A37'),

			'bottom-textcolor'              => $gantry->get('bottom-textcolor',             '#8582A5'),
			'bottom-background'             => $gantry->get('bottom-background',            '#1D1A37'),

			'footer-textcolor'              => $gantry->get('footer-textcolor',             '#8582A5'),
			'footer-background'             => $gantry->get('footer-background',            '#1D1A37'),

			'copyright-textcolor'           => $gantry->get('copyright-textcolor',          '#777496'),
			'copyright-background'          => $gantry->get('copyright-background',         '#252243')

		);

		// Section Background Images
		$positions  = array('slideshow-customslideshow-image');
		$source     = "";

		foreach ($positions as $position) {
			$data = $gantry->get($position, false) ? json_decode(str_replace("'", '"', $gantry->get($position))) : false; 
			if ($data) $source = $data->path;
			$lessVariables[$position] = $data ? 'url(' . $source . ')' : 'none';
		}

		$gantry->addInlineStyle($css);

		$gantry->addLess('global.less', 'master.css', 7, $lessVariables);

		$this->_disableRokBoxForiPhone();

		if ($gantry->get('layout-mode')=="responsive") {
			$gantry->addLess('mediaqueries.less');
			$gantry->addLess('grid-flexbox-responsive.less');
			$gantry->addLess('menu-dropdown-direction.less');
		}
		
		if ($gantry->get('layout-mode')=="960fixed")   $gantry->addLess('960fixed.less');
		if ($gantry->get('layout-mode')=="1200fixed")  $gantry->addLess('1200fixed.less');

		// Scrolling Header
		if ($gantry->get('header-type')=="scroll") {
			$gantry->addScript('scrolling-header-fullpage.js');
		}

		// RTL
		if ($gantry->get('rtl-enabled') && is_rtl()) $gantry->addLess('rtl.less', 'rtl.css', 8, $lessVariables);

		// Demo Styling
		if ($gantry->get('demo')) $gantry->addLess('demo.less', 'demo.css', 9, $lessVariables);

		// Chart Script
		if ($gantry->get('chart-enabled')) $gantry->addScript('chart.js');

		// Animate
		if ($gantry->get('animate')){
			// WOW JS
			if ($ie_ver != 'ie8') {
				$gantry->addLess('animate.less');
				$gantry->addScript('wow.js');
				$gantry->addScript('wow-init.js');
			}
		}

		// Odometer
		if ($gantry->get('odometer')){
			$gantry->addLess('odometer.less');
			$gantry->addScript('odometer.js');
			$gantry->addScript('odometer-init.js');
		}

		// Particles JS
		if (($gantry->get('slideshow-type') == 'animation') && ($g_platform != 'mobile')) {
			if ($ie_ver != 'oldIE') {
				$gantry->addScript('particles.js');
			}
		}

		// SmoothScroll
		$gantry->addScript('bind-polyfill.min.js');
		$gantry->addScript('smooth-scroll.min.js');

		// Add inline css from the Custom CSS field
		$gantry->addInlineStyle($gantry->get('customcss'));
	}

	function buildLogo(){
		global $gantry;

		if ($gantry->get('logo-type')!="custom") return "";

		$source = $width = $height = "";

		$logo = str_replace("&quot;", '"', str_replace("'", '"', $gantry->get('logo-custom-image')));
		$data = json_decode($logo);

		if (!$data){
			if (strlen($logo)) $source = $logo;
			else return "";
		} else {
			$source = $data->path;
		}

		if(!is_ssl()) {
			if(substr($source, 0, 5) == 'https') {
				$source = str_replace('https', 'http', $source);
			}
		} else {
			if(substr($source, 0, 5) != 'https') {
				$source = str_replace('http', 'https', $source);
			}
		}

		$baseUrl = trailingslashit(site_url());

		if (substr($baseUrl, 0, strlen($baseUrl)) == substr($source, 0, strlen($baseUrl))){
			$file = trailingslashit(ABSPATH) . substr($source, strlen($baseUrl));
		} else {
			$file = trailingslashit(ABSPATH) . $source;
		}

		if (isset($data->width) && isset($data->height)){
			$width = $data->width;
			$height = $data->height;
		} else {
			$size = @getimagesize($file);
			$width = $size[0];
			$height = $size[1];
		}

		$source = str_replace(' ', '%20', $source);

		$output = "";
		$output .= "#rt-logo {background: url(".$source.") 50% 0 no-repeat !important;}"."\n";
		$output .= "#rt-logo {width: ".$width."px;height: ".$height."px;}"."\n";

		$file = preg_replace('/\//i', DIRECTORY_SEPARATOR, $file);

		return (file_exists($file)) ? $output : '';
	}

	function _disableRokBoxForiPhone() {
		global $gantry;

		if ($gantry->browser->platform == 'iphone' || $gantry->browser->platform == 'android') {
			$gantry->addInlineScript("window.addEvent('domready', function() {\$\$('a[rel^=rokbox]').removeEvents('click');});");
		}
	}
}