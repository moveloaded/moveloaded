<?php
/**
 * Template Name: Contact Form
 */
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( 'ABSPATH' ) or die( 'Restricted access' );

global $gantry;

$gantry->addBodyClass( 'menu-contact-us' );

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
<?php /* Head */
	$gantry->displayHead();
?>
<?php /* Force IE to Use the Most Recent Version */ if ($gantry->browser->name == 'ie') : ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php endif; ?>

<?php
	$gantry->addStyle('grid-responsive.css', 5);
	$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);
	if ($gantry->browser->name == 'ie'){
		if ($gantry->browser->shortversion == 8){
			$gantry->addScript('html5shim.js');
			$gantry->addScript('canvas-unsupported.js');
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
<body <?php echo $gantry->displayBodyTag(); ?>>
	<div id="rt-page-surround">
		<?php /** Begin Header Surround **/ if ($gantry->countModules('drawer') or $gantry->countModules('top') or $gantry->countModules('header') or $gantry->countModules('slideshow')) : ?>
		<header id="rt-header-surround">
			<?php /** Begin Drawer **/ if ($gantry->countModules('drawer')) : ?>
			<div id="rt-drawer">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('drawer','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Drawer **/ endif; ?>
			<?php /** Begin Top **/ if ($gantry->countModules('top')) : ?>
			<div id="rt-top">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('top','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Top **/ endif; ?>
			<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
			<div id="rt-header">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('header','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<div class="rt-header-fixed-spacer"></div>
			<?php /** End Header **/ endif; ?>
			<?php /** Begin Slideshow **/ if ($gantry->countModules('slideshow')) : ?>
			<div id="rt-slideshow">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('slideshow','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Slideshow **/ endif; ?>
		</header>
		<?php /** End Header Surround **/ endif; ?>

		<?php /** Begin Showcase Section **/ if ($gantry->countModules('showcase')) : ?>
		<section id="rt-showcase-surround">
			<?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
			<div id="rt-showcase">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('showcase','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Showcase **/ endif; ?>
		</section>
		<?php /** End Showcase Section **/ endif; ?>

		<?php /** Begin FirstFullWidth **/ if ($gantry->countModules('firstfullwidth')) : ?>
		<section id="rt-firstfullwidth">
			<?php echo $gantry->displayModules('firstfullwidth','basic','standard'); ?>
			<div class="clear"></div>
		</section>
		<?php /** End FirstFullWidth **/ endif; ?>

		<?php /** Begin Neck Section **/ if ($gantry->countModules('breadcrumb') or $gantry->countModules('utility') or $gantry->countModules('feature')) : ?>
		<section id="rt-neck-surround">
			<?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
			<div id="rt-breadcrumbs">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('breadcrumb','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Breadcrumbs **/ endif; ?>
			<?php /** Begin Utility **/ if ($gantry->countModules('utility')) : ?>
			<div id="rt-utility">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('utility','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Utility **/ endif; ?>
			<?php /** Begin Feature **/ if ($gantry->countModules('feature')) : ?>
			<div id="rt-feature">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('feature','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Feature **/ endif; ?>
		</section>
		<?php /** End Neck Section **/ endif; ?>

		<?php /** Begin SecondFullWidth **/ if ($gantry->countModules('secondfullwidth')) : ?>
		<section id="rt-secondfullwidth">
			<?php echo $gantry->displayModules('secondfullwidth','basic','standard'); ?>
			<div class="clear"></div>
		</section>
		<?php /** End SecondFullWidth **/ endif; ?>

		<?php /** Begin MainTop Section **/ if ($gantry->countModules('maintop') or $gantry->countModules('expandedtop')) : ?>
		<section id="rt-maintop-surround">
			<?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
			<div id="rt-maintop">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('maintop','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Main Top **/ endif; ?>
			<?php /** Begin Expanded Top **/ if ($gantry->countModules('expandedtop')) : ?>
			<div id="rt-expandedtop">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('expandedtop','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Expanded Top **/ endif; ?>
		</section>
		<?php /** End MainTop Section **/ endif; ?>

		<?php /** Begin MainBottom Section **/ if ($gantry->countModules('expandedbottom') or $gantry->countModules('mainbottom')) : ?>
		<section id="rt-mainbottom-surround">
			<?php /** Begin Expanded Bottom **/ if ($gantry->countModules('expandedbottom')) : ?>
			<div id="rt-expandedbottom">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('expandedbottom','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Expanded Bottom **/ endif; ?>
			<?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
			<div id="rt-mainbottom">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('mainbottom','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php /** End Main Bottom **/ endif; ?>
		</section>
		<?php /** End MainBottom Section **/ endif; ?>

		<section id="rt-mainbody-surround">
			<?php /** Begin Main Body **/ ?>
			<div class="rt-container">
				<?php echo $gantry->displayMainbody('contactform','sidebar','standard','standard','standard','standard','standard'); ?>
			</div>
			<?php /** End Main Body **/ ?>
		</section>

		<?php /** Begin ThirdFullWidth **/ if ($gantry->countModules('thirdfullwidth')) : ?>
		<section id="rt-thirdfullwidth">
			<?php echo $gantry->displayModules('thirdfullwidth','basic','standard'); ?>
			<div class="clear"></div>
		</section>
		<?php /** End ThirdFullWidth **/ endif; ?>

		<?php /** Begin Extension Section **/ if ($gantry->countModules('extension')) : ?>
		<section id="rt-extension-surround">
			<div id="rt-extension">
				<div class="rt-container">
					<div class="rt-flex-container">
						<?php echo $gantry->displayModules('extension','standard','standard'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</section>
		<?php /** End Extension Section **/ endif; ?>

		<?php /** Begin Footer Section **/ if ($gantry->countModules('bottom') or $gantry->countModules('footer') or $gantry->countModules('totop') or $gantry->countModules('copyright')) : ?>
		<footer id="rt-footer-surround">
			<div class="rt-footer-surround-pattern">
				<?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
				<div id="rt-bottom">
					<div class="rt-container">
						<div class="rt-flex-container">
							<?php echo $gantry->displayModules('bottom','standard','standard'); ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<?php /** End Bottom **/ endif; ?>
				<?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
				<div id="rt-footer">
					<div class="rt-container">
						<div class="rt-flex-container">
							<?php echo $gantry->displayModules('footer','standard','standard'); ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<?php /** End Footer **/ endif; ?>
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
		<?php /** End Footer Surround **/ endif; ?>

		<?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
		<div id="rt-debug">
			<div class="rt-container">
				<div class="rt-flex-container">
					<?php echo $gantry->displayModules('debug','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<?php /** End Debug **/ endif; ?>

		<?php /** Begin Analytics **/ if ($gantry->countModules('analytics')) : ?>
		<?php echo $gantry->displayModules('analytics','basic','basic'); ?>
		<?php /** End Analytics **/ endif; ?>

		<?php /** Popup Login and Popup Module **/ ?>
		<?php echo $gantry->displayModules('login','login','popup'); ?>
		<?php echo $gantry->displayModules('popup','popup','popup'); ?>
		<?php /** End Popup Login and Popup Module **/ ?>
	</div>

	<?php global $g_platform; ?>
    <?php if ( $gantry->countModules('slideshow') && $gantry->get('slideshow-type') == 'animation' && ($g_platform != 'mobile') ) : ?>
		<script type="text/javascript">
			particlesJS('rt-slideshow', {
			  particles: {
			    color: '#000',
			    color_random: false,
			    shape: 'circle', // "circle", "edge" or "triangle"
			    opacity: {
			      opacity: 0.25,
			      anim: {
			        enable: true,
			        speed: 0.25,
			        opacity_min: 0.05,
			        sync: false
			      }
			    },
			    size: 20,
			    size_random: false,
			    nb: 100,
			    line_linked: {
			      enable_auto: true,
			      distance: 200,
			      color: '#000',
			      opacity: 0.85,
			      width: 0.75,
			      condensed_mode: {
			        enable: false,
			        rotateX: 800,
			        rotateY: 800
			      }
			    },
			    anim: {
			      enable: true,
			      speed: 1
			    }
			  },
			  interactivity: {
			    enable: false
			  },
			  /* Retina Display Support */
			  retina_detect: true
			});
		</script>
    <?php endif; ?>

    <?php $gantry->displayFooter(); ?>
</body>
</html>
<?php
$gantry->finalize();
?>