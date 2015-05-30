<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( 'ABSPATH' ) or die( 'Restricted access' );
global $validated, $emailSent, $name_error, $email_error, $subject_error, $message_error, $recaptcha_error;
?>

			<?php /** Begin Page **/ ?>
					
			<div <?php post_class('contact-form-page'); ?> id="post-<?php the_ID(); ?>">

				<?php /** Begin Article Title **/ ?>

				<?php if( $gantry->get( 'page-title-enabled', '1' ) ) : ?>

					<h2>
						<?php if( $gantry->get( 'page-title-link', '0' ) ) : ?>
							<a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( get_the_title() ); ?>"><?php the_title(); ?></a>
						<?php else : ?>
							<?php the_title(); ?>
						<?php endif; ?>
					</h2>

				<?php endif; ?>

				<?php /** End Article Title **/ ?>

				<?php /** Begin Extended Meta **/ ?>

				<?php if( $gantry->get( 'page-meta-author-enabled', '0' ) || $gantry->get( 'page-meta-date-enabled', '0' ) || $gantry->get( 'page-meta-modified-enabled', '0' ) || $gantry->get( 'page-meta-comments-enabled', '0' ) ) : ?>
				
					<dl class="article-info">

						<?php /** Begin Date & Time **/ ?>

						<?php if( $gantry->get( 'page-meta-date-enabled', '0' ) ) : ?>

							<dd class="create"> <?php if( $gantry->get( 'page-meta-date-prefix' ) != '' ) echo $gantry->get( 'page-meta-date-prefix' ) . ' '; ?><?php the_time( $gantry->get( 'page-meta-date-format', 'd F Y' ) ); ?></dd>

						<?php endif; ?>

						<?php /** End Date & Time **/ ?>

						<?php /** Begin Modified Date **/ ?>

						<?php if( $gantry->get( 'page-meta-modified-enabled', '0' ) ) : ?>

							<dd class="modified"> <?php if( $gantry->get( 'page-meta-modified-prefix' ) != '' ) echo $gantry->get( 'page-meta-modified-prefix' ) . ' '; ?><?php the_modified_date( $gantry->get( 'page-meta-modified-format', 'd F Y' ) ); ?></dd>

						<?php endif; ?>

						<?php /** End Modified Date **/ ?>

						<?php /** Begin Author **/ ?>
					
						<?php if( $gantry->get( 'page-meta-author-enabled', '0' ) ) : ?>

							<dd class="createdby"> 
								<?php if( $gantry->get( 'page-meta-author-prefix' ) != '' ) echo $gantry->get( 'page-meta-author-prefix' ) . ' '; ?>
								
								<?php if( $gantry->get( 'page-meta-author-link', '1' ) ) : ?>
									<?php the_author_posts_link(); ?>
								<?php else : ?>
									<?php the_author(); ?>
								<?php endif; ?>
							</dd>

						<?php endif; ?>

						<?php /** End Author **/ ?>

						<?php /** Begin Comments Count **/ ?>

						<?php if( $gantry->get( 'page-meta-comments-enabled', '0' ) ) : ?>

							<?php if( $gantry->get( 'page-meta-comments-link', '0' ) ) : ?>

								<dd class="comments-count"> 
									<a href="<?php comments_link(); ?>">
										<?php comments_number( __( '0 Comments', 'rt_cygnet_wp_lang' ), __( '1 Comment', 'rt_cygnet_wp_lang' ), __( '% Comments', 'rt_cygnet_wp_lang' ) ); ?>
									</a>
								</dd>

							<?php else : ?>

								<dd class="comments-count"> <?php comments_number( __( '0 Comments', 'rt_cygnet_wp_lang' ), __( '1 Comment', 'rt_cygnet_wp_lang' ), __( '% Comments', 'rt_cygnet_wp_lang' ) ); ?></dd>

							<?php endif; ?>

						<?php endif; ?>

						<?php /** End Comments Count **/ ?>

					</dl>
				
				<?php endif; ?>

				<?php /** End Extended Meta **/ ?>

				<?php /** Begin Featured Image **/ ?>
			
				<?php if( $gantry->get( 'page-featured-image', '0' ) && function_exists( 'the_post_thumbnail' ) && has_post_thumbnail() ) : ?>

					<div class="img-fulltext-<?php echo $gantry->get( 'thumb-position', 'left' ); ?>">
						<p>
							<?php the_post_thumbnail( 'gantryThumb', array( 'class' => 'rt-image ' ) ); ?>			
						</p>
					</div>
				
				<?php endif; ?>

				<?php /** End Featured Image **/ ?>
						
				<?php /** Begin Post Content **/ ?>

				<div class="post-content">	
						
					<?php the_content(); ?>

				</div>

				<?php edit_post_link( __( 'Edit', 'rt_cygnet_wp_lang' ), '<div class="edit-link">', '</div>' ); ?>

				<?php /** End Post Content **/ ?>

				<?php /** Begin Contact Form **/ ?>

				<?php if( isset( $emailSent ) && $emailSent == true ) : ?>

					<p class="success contact-form-success">
						<?php _e( 'Your email was sent successfully. Thank you!', 'rt_cygnet_wp_lang' ); ?>
					</p>

					<p class="back-to-home">
						<a href="<?php echo home_url(); ?>" class="button"><span class="icon-arrow-left"></span> <?php _e( 'Back to the homepage', 'rt_cygnet_wp_lang' ); ?></a>
					</p>

				<?php else : ?>

					<?php if( isset($hasError) || !$validated ) : ?>

						<p class="error contact-form-error">
							<?php _e( 'Sorry, an error occured. Please try again.', 'rt_cygnet_wp_lang' ); ?>
						</p>

					<?php endif; ?>

					<div class="contact-form">
						<form action="<?php the_permalink(); ?>" id="contact-form" class="form-validate form-horizontal" method="post">

							<?php if( $gantry->get( 'contact-header' ) != '' ) echo '<legend>' . $gantry->get( 'contact-header' ) . '</legend>'; ?>
												
							<div class="control-group<?php if($name_error != '') echo ' error'; ?>">
								<div class="control-label">
									<label for="rt-contact-name"><?php _e( 'Name', 'rt_cygnet_wp_lang' ); ?> *</label>
								</div>
								<div class="controls">
									<input type="text" name="rt-contact-name" id="rt-contact-name" value="<?php if( isset( $_POST['rt-contact-name'] ) ) echo $_POST['rt-contact-name'];?>" />
									<?php if($name_error != '') : ?>
									<span class="help-inline">
										<?php echo $name_error; ?>
									</span>
									<?php endif; ?>
								</div>
							</div>
							
							<div class="control-group<?php if($email_error != '') echo ' error'; ?>">
								<div class="control-label">
									<label for="rt-contact-email"><?php _e( 'Email', 'rt_cygnet_wp_lang' ); ?> *</label>
								</div>
								<div class="controls">
									<input type="text" name="rt-contact-email" id="rt-contact-email" value="<?php if( isset( $_POST['rt-contact-email'] ) ) echo $_POST['rt-contact-email'];?>" />
									<?php if($email_error != '') : ?>
									<span class="help-inline">
										<?php echo $email_error; ?>
									</span>
									<?php endif; ?>
								</div>
							</div>

							<div class="control-group<?php if($subject_error != '') echo ' error'; ?>">
								<div class="control-label">
									<label for="rt-contact-subject"><?php _e( 'Subject', 'rt_cygnet_wp_lang' ); ?> *</label>
								</div>
								<div class="controls">
									<input type="text" name="rt-contact-subject" id="rt-contact-subject" value="<?php if( isset( $_POST['rt-contact-subject'] ) ) echo $_POST['rt-contact-subject'];?>" />
									<?php if($subject_error != '') : ?>
									<span class="help-inline">
										<?php echo $subject_error; ?>
									</span>
									<?php endif; ?>
								</div>
							</div>
							
							<div class="control-group<?php if($message_error != '') echo ' error'; ?>">
								<div class="control-label">
									<label for="rt-contact-message"><?php _e( 'Message', 'rt_cygnet_wp_lang' ); ?> *</label>
								</div>
								<div class="controls">
									<textarea name="rt-contact-message" id="rt-contact-message" rows="8"></textarea>
									<?php if($message_error != '') : ?>
									<span class="help-inline">
										<?php echo $message_error; ?>
									</span>
									<?php endif; ?>
								</div>
							</div>

							<?php if( $gantry->get( 'contact-recaptcha-enabled', '0' ) && $gantry->get( 'contact-recaptcha-privatekey' ) != '' && $gantry->get( 'contact-recaptcha-publickey' ) != '' ) : ?>
								
								<script type="text/javascript">var RecaptchaOptions = { theme : 'white' };</script>

								<?php if($recaptcha_error != '') : ?>
									<p class="error contact-form-error">
										<?php echo $recaptcha_error; ?>
									</p>
								<?php endif; ?>

								<?php 
									$publickey = $gantry->get( 'contact-recaptcha-publickey' );
									echo recaptcha_get_html($publickey); 
								?>

							<?php endif; ?>

							<div class="control-group">
								<div class="control-label">
									<label class="checkbox send-copy">
										<?php _e( 'Send copy to yourself', 'rt_cygnet_wp_lang' ); ?>
									</label>
								</div>
								<div class="controls">
									<input type="checkbox" name="rt-send-copy" id="rt-send-copy" value="true" />
								</div>
							</div>
							
							<div class="form-actions">
								<button class="btn btn-primary validate" type="submit" name="submit" id="submit"><?php _e( 'Send Email', 'rt_cygnet_wp_lang' ); ?></button>
								<input type="hidden" name="submitted" id="submitted" value="true" />
							</div>
							
						</form>
					</div>

				<?php endif; ?>

				<?php /** End Contact Form **/ ?>

				<?php /** Begin Comments **/ ?>
					
				<?php if( comments_open() && $gantry->get( 'page-comments-form-enabled', '0' ) ) : ?>
															
					<?php echo $gantry->displayComments( true, 'standard', 'standard' ); ?>
				
				<?php endif; ?>

				<?php /** End Comments **/ ?>

			</div>
			
			<?php /** End Page **/ ?>