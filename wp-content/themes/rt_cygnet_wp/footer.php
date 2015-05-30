<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( 'ABSPATH' ) or die( 'Restricted access' );

global $gantry; ?>					
				<?php echo $gantry->displayMainbody('mainbody','sidebar','standard','standard','standard','standard','standard', null, ob_get_clean()); ?>
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