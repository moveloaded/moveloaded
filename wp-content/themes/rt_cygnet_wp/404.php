<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('ABSPATH') or die('Restricted access');

global $gantry;

// get the current preset
$gpreset = str_replace(' ','',strtolower($gantry->get('name')));
?>
<!doctype html>
<html xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
	<head>
		<?php if ($gantry->get('layout-mode') == '960fixed') : ?>
		<meta name="viewport" content="width=960px">
		<?php elseif ($gantry->get('layout-mode') == '1200fixed') : ?>
		<meta name="viewport" content="width=1200px">
		<?php else : ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php endif; ?>

		<?php
		$gantry->displayHead();

		// Less Variables
		$lessVariables = array(
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

			'footer-textcolor'              => $gantry->get('footer-textcolor',             '#8582A5'),
			'footer-background'             => $gantry->get('footer-background',            '#1D1A37'),

			'copyright-textcolor'           => $gantry->get('copyright-textcolor',          '#777496'),
			'copyright-background'          => $gantry->get('copyright-background',         '#252243')
		);

		$gantry->addStyle('grid-responsive.css', 5);
		$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);
		$gantry->addLess('error.less', 'error.css', 4, $lessVariables);

		// Scripts
		if ($gantry->browser->name == 'ie'){
			if ($gantry->browser->shortversion == 8){
				$gantry->addScript('html5shim.js');
				$gantry->addScript('placeholder-ie.js');
			}
			if ($gantry->browser->shortversion == 9){
				$gantry->addInlineScript("if (typeof RokMediaQueries !== 'undefined') window.addEvent('domready', function(){ RokMediaQueries._fireEvent(RokMediaQueries.getQuery()); });");
				$gantry->addScript('placeholder-ie.js');
			}
		}
		if ($gantry->get('layout-mode', 'responsive') == 'responsive') $gantry->addScript('rokmediaqueries.js');
		?>
	</head>
	<body id="rt-error" <?php echo $gantry->displayBodyTag(); ?>>
		<div id="rt-page-surround">
			<?php /** Begin Header Surround **/ ?>
			<header id="rt-header-surround">	
				<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
				<div id="rt-header">
					<div class="rt-container">
						<div class="rt-flex-container">
							<?php echo $gantry->displayModules('header','standard','standard'); ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<?php /** End Header **/ endif; ?>
			</header>
			<?php /** End Header Surround **/ ?>

			<?php /** Begin Showcase Section **/ ?>
			<section id="rt-showcase-surround">
				<div id="rt-showcase-spacer"></div>
				<div id="rt-showcase">
					<div class="rt-container">
						<div class="rt-flex-container">
							<div class="rt-error-body">
								<div class="rt-error-header">
									<div class="rt-error-code">404</div>
									<div class="rt-error-code-desc"><?php _e( 'Page not found', 'rt_cygnet_wp_lang' ); ?></div>
								</div>
								<div class="rt-error-content">
									<div class="rt-error-title"><?php _e( 'Oh my gosh! You found it!!!', 'rt_cygnet_wp_lang' ); ?></div>
									<div class="rt-error-message"><?php _e( "Looks like the page you're trying to visit doesn't exist.<br />Please check the URL and try your luck again.", 'rt_cygnet_wp_lang' ); ?></div>
									<div class="rt-error-button"><a href="<?php echo home_url(); ?>" class="readon"><span><?php _e( 'Take Me Home', 'rt_cygnet_wp_lang' ); ?></span></a></div>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<?php /** End Showcase Section **/ ?>

			<?php /** Begin Footer Section **/ ?>
			<footer id="rt-footer-surround">
				<div class="rt-footer-surround-pattern">
					<?php /** Begin ToTop **/ if ($gantry->countModules('totop')) : ?>
					<div id="rt-totop">
						<?php echo $gantry->displayModules('totop','basic','basic'); ?>
						<div class="clear"></div>
					</div>
					<?php /** End ToTop **/ endif; ?>				
					<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
					<div id="rt-copyright">
						<div class="rt-container">
							<div class="rt-flex-container">
								<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					<?php /** End Copyright **/ endif; ?>
				</div>
			</footer>		
			<?php /** End Footer Surround **/ ?>
		</div>
		<?php $gantry->displayFooter(); ?>
	</body>
</html>
<?php
$gantry->finalize();
?>