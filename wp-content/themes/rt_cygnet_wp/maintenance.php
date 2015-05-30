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

$gantry->addBodyClass( 'menu-maintenance' );

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

		$gantry->pageTitle = __( 'Offline Page', 'rt_cygnet_wp_lang' );

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
		$gantry->addLess('offline.less', 'offline.css', 4, $lessVariables);

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
	<body id="rt-offline" <?php echo $gantry->displayBodyTag(); ?>>
		<div id="rt-page-surround">
			<section id="rt-showcase-surround">
				<div id="rt-showcase">
					<div class="rt-container">
						<div class="rt-flex-container">
							<div class="rt-offline-body">
								<div class="rt-logo-block rt-offline-logo">
									<a id="rt-logo" href="<?php echo home_url(); ?>"></a>
								</div>

								<div class="rt-offline-title rt-big-title rt-center">
									<div class="module-title">
										<h2 class="title"><?php _e( 'Our Website is Temporarily Offline', 'rt_cygnet_wp_lang' ); ?></h2>
									</div>
								</div>

								<?php if( $gantry->get('maintenancemode-message') != '' ) : ?>
								<p class="rt-offline-message">
									<?php echo $gantry->get('maintenancemode-message'); ?>
								</p>
								<?php endif; ?>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</section>

			<?php if ($gantry->get('email-subscription-enabled')) : ?>
			<section id="rt-utility-surround">
				<div id="rt-utility">
					<div class="rt-container">
						<div class="rt-flex-container">
								<section id="rt-subscription-form">
									<p class="rt-subscription-title">
										<?php _e( 'Get Notified When We Release', 'rt_cygnet_wp_lang' ); ?>
									</p>
									<form class="rt-offline-form" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $gantry->get('feedburner-uri'); ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
										<input type="text" placeholder="<?php _e( 'Email Address', 'rt_cygnet_wp_lang' ); ?>" class="inputbox" name="email">
										<input type="hidden" value="<?php echo $gantry->get('feedburner-uri'); ?>" name="uri"/>
										<input type="hidden" name="loc" value="en_US"/>
										<input type="submit" name="Submit" class="readon" value="<?php _e( 'Subscribe', 'rt_cygnet_wp_lang' ); ?>" />
									</form>
									<div class="clear"></div>
								</section>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</section>
			<?php endif; ?>

			<footer id="rt-footer-surround">
				<div id="rt-footer">
					<div class="rt-container">
						<div class="rt-flex-container">
							<div id="rt-authorized-form">
								<h2 class="rt-authorized-form-title"><?php _e( 'Authorized Login', 'rt_cygnet_wp_lang' ); ?></h2>

								<p class="rt-authorized-login-message">
									<?php _e( 'When your site is in Maintenance mode, the site frontend will not be available for public. You can still access the frontend of the site by logging in as an administrator below. You can customize this message in the Cygnet theme language file.', 'rt_cygnet_wp_lang' ); ?>
								</p>

								<?php if(!is_super_admin()): ?>
									<form class="rt-authorized-login-form" action="<?php echo wp_login_url($_SERVER['REQUEST_URI']); ?>" method="post" id="rt-form-login">
										<input name="log" id="username" class="inputbox" type="text" placeholder="<?php _e( 'User Name', 'rt_cygnet_wp_lang' ); ?>" />
										<input type="password" name="pwd" class="inputbox" placeholder="<?php _e( 'Password', 'rt_cygnet_wp_lang' ); ?>" />
										<input type="hidden" name="rememberme" class="inputbox" value="yes" id="remember" />
										<input type="submit" name="Submit" class="readon" value="<?php _e( 'Log in', 'rt_cygnet_wp_lang' ); ?>" />				
									</form>
								<?php endif; ?>
								<?php if(is_super_admin()): ?>
									<form class="rt-authorized-login-form" id="rt-form-login">
										<a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>" class="readon" title="<?php _e( 'Log out', 'rt_cygnet_wp_lang' ); ?>"><?php _e( 'Log out', 'rt_cygnet_wp_lang' ); ?></a>
									</form>	
								<?php endif; ?>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div id="rt-copyright">
					<div class="rt-container">
						<div class="rt-flex-container">
							<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</footer>
		</div>
		<?php $gantry->displayFooter(); ?>
	</body>
</html>
<?php
$gantry->finalize();
?>