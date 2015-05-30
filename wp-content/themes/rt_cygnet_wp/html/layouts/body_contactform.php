<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Contact form based on the original code by Orman Clark
 * http://www.premiumpixels.com
 */
defined( 'GANTRY_VERSION' ) or die();

gantry_import( 'core.gantrybodylayout' );

global $gantry;

/**
 *
 * @package gantry
 * @subpackage html.layouts
 */
class GantryLayoutBody_ContactForm extends GantryBodyLayout {
	var $render_params = array(
		'schema'        =>  null,
		'pushPull'      =>  null,
		'classKey'      =>  null,
		'sidebars'      =>  '',
		'contentTop'    =>  null,
		'contentBottom' =>  null,
		'component_content' => ''
	);

	function render( $params = array() ) {
		global $gantry, $post, $posts, $query_string, $validated, $emailSent, $name_error, $email_error, $subject_error, $message_error, $recaptcha_error;

		$fparams = $this-> _getParams( $params );

		// load the reCAPTCHA library?
		if( $gantry->get( 'contact-recaptcha-enabled', '0' ) ) {
			require_once( get_template_directory() . '/lib/recaptcha/recaptchalib.php' );
		}

		// logic to determine if the component should be displayed
		$display_mainbody = !( $gantry->get( 'mainbody-enabled', true ) == false );
		$display_component = !( $gantry->get( 'component-enabled', true ) == false );
		
		$name_error = '';
		$email_error = '';
		$subject_error = '';
		$message_error = '';

		# the response from reCAPTCHA
		$resp = null;
		# the error code from reCAPTCHA, if any
		$error = null;
		# validation helper
		$validated = true;
		
		if( isset( $_POST['submitted'] ) ) {
		
			if( trim( $_POST['rt-contact-name'] ) === '' ) {
				$name_error = __( 'Please enter your name.', 'rt_cygnet_wp_lang' );
				$hasError = true;
				$validated = false;
			} else {
				$name = trim( $_POST['rt-contact-name'] );
			}
			
			if( trim( $_POST['rt-contact-email'] ) === '' )  {
				$email_error = __( 'Please enter your email address.', 'rt_cygnet_wp_lang' );
				$hasError = true;
				$validated = false;
			} else if ( !eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim( $_POST['rt-contact-email'] ) ) ) {
				$email_error = __( 'You entered an invalid email address.', 'rt_cygnet_wp_lang' );
				$hasError = true;
				$validated = false;
			} else {
				$email = trim( $_POST['rt-contact-email'] );
			}

			if( trim( $_POST['rt-contact-subject'] ) === '' ) {
				$subject_error = __( 'Please enter subject of your message.', 'rt_cygnet_wp_lang' );
				$hasError = true;
				$validated = false;
			} else {
				$subject = trim( $_POST['rt-contact-subject'] );
			}
				
			if( trim( $_POST['rt-contact-message'] ) === '' ) {
				$message_error = __( 'Please enter a message.', 'rt_cygnet_wp_lang' );
				$hasError = true;
				$validated = false;
			} else {
				$comments = stripslashes( trim( $_POST['rt-contact-message'] ) );
			}

			// reCaptcha validation
			if( $gantry->get( 'contact-recaptcha-enabled', '0' ) && $gantry->get( 'contact-recaptcha-privatekey' ) != '' && $gantry->get( 'contact-recaptcha-publickey' ) != '' ) {
				$privatekey = $gantry->get( 'contact-recaptcha-privatekey' );
				$resp = recaptcha_check_answer(
					$privatekey,
					$_SERVER["REMOTE_ADDR"],
					$_POST["recaptcha_challenge_field"],
					$_POST["recaptcha_response_field"]
					);

				if (!$resp->is_valid) {
					$validated = false;
					$hasError = true;
					$recaptcha_error = __( 'The entered reCaptcha is incorrect. Please try again.', 'rt_cygnet_wp_lang' );
				}
			}
				
			if( !isset( $hasError ) && $validated ) {
				$emailTo = $gantry->get( 'contact-email' );
				if ( !isset( $emailTo ) || ( $emailTo == '' ) ) {
					$emailTo = get_option( 'admin_email' );
				}
				$subject = __( '[Contact Form] ' . get_bloginfo( 'name' ), 'rt_cygnet_wp_lang' ) . ' : ' . $subject;
				$body = __( 'From:', 'rt_cygnet_wp_lang' ) . " $name \n\n" . __( 'Email:', 'rt_cygnet_wp_lang' ) . " $email \n\n" . $comments;
				$headers = __( 'From:', 'rt_cygnet_wp_lang' ) . ' ' . $name . ' <' . $emailTo . '> ' . "\n\n";
				
				if( isset( $_POST['rt-send-copy'] ) && $_POST['rt-send-copy'] == true ) {
					mail( $email, $subject, $body, $headers );
				}
				
				mail( $emailTo, $subject, $body, $headers );
				$emailSent = true;
			}
			
		}
		
		ob_start();
		// XHTML LAYOUT
		?>

		<?php if ( $display_mainbody ) : ?>
		<div id="rt-main" class="<?php echo $fparams->classKey; ?>">
			<div class="rt-flex-container">
				<?php /* MainBody Wrapper */ ?>
				<div class="rt-mainbody-wrapper rt-grid-<?php echo $fparams->schema['mb']; ?> <?php echo $fparams->pushPull[0]; ?>">

					<?php if( isset( $fparams->contentTop ) ) : ?>
					<div id="rt-content-top">
						<div class="rt-flex-container">
							<?php echo $fparams->contentTop; ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if ( $display_component ) : ?>
					<div class="rt-component-block rt-block">
						<div id="rt-mainbody">
							<div class="component-content">
								
								<?php /** Begin Contact Form Template **/ ?>

								<div class="item-page contact">

								<?php if ( have_posts() ) : ?>

									<?php /** Begin Page Heading **/ ?>

									<?php if( $gantry->get( 'page-page-heading-enabled', '0' ) && $gantry->get( 'page-page-heading-text' ) != '' ) : ?>
									
										<h1>
											<?php echo $gantry->get( 'page-page-heading-text' ); ?>
										</h1>
									
									<?php endif; ?>
									
									<?php /** End Page Heading **/ ?>

									<?php while ( have_posts() ) : the_post(); ?>

										<?php $this->get_content_template( 'content/content', 'contactpage', false ); ?>
									
									<?php endwhile; ?>
								
								<?php else : ?>
																							
									<h1>
										<?php _e( 'Sorry, no pages matched your criteria.', 'rt_cygnet_wp_lang' ); ?>
									</h1>
									
								<?php endif; ?>

								</div>

								<?php wp_reset_query(); ?>

								<?php /** End Contact Form Template **/ ?>

							</div>
						</div>
					</div>
					<?php endif; ?>

					<?php if( isset( $fparams->contentBottom ) ) : ?>
					<div id="rt-content-bottom">
						<div class="rt-flex-container">
							<?php echo $fparams->contentBottom; ?>
						</div>
					</div>
					<?php endif; ?>

				</div>

				<?php /* SideBar Wrapper */ ?>
				<?php echo $fparams->sidebars; ?>

				<div class="clear"></div>
			</div>
		</div>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}
}