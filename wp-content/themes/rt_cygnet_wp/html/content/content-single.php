<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( 'ABSPATH' ) or die( 'Restricted access' );

// Create a shortcut for params.
$category = get_the_category();
?>

			<?php /** Begin Post **/ ?>
					
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">

				<?php /** Begin Article Title **/ ?>

				<?php if( $gantry->get( 'single-title-enabled', '1' ) ) : ?>

					<h2>
						<?php if( $gantry->get( 'single-title-link', '0' ) ) : ?>
							<a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( get_the_title() ); ?>"><?php the_title(); ?></a>
						<?php else : ?>
							<?php the_title(); ?>
						<?php endif; ?>
					</h2>

				<?php endif; ?>

				<?php /** End Article Title **/ ?>

				<?php /** Begin Extended Meta **/ ?>

				<?php if( $gantry->get( 'single-meta-author-enabled', '1' ) || $gantry->get( 'single-meta-category-enabled', '0' ) || $gantry->get( 'single-meta-category-parent-enabled', '0' ) || $gantry->get( 'single-meta-date-enabled', '1' ) || $gantry->get( 'single-meta-modified-enabled', '0' ) || $gantry->get( 'single-meta-comments-enabled', '1' ) ) : ?>
				
					<dl class="article-info">

						<?php /** Begin Parent Category **/ ?>
							
						<?php if( $gantry->get( 'single-meta-category-parent-enabled', '0' ) && !empty( $category ) && $category[0]->parent != '0' ) : ?>

							<dd class="parent-category-name"> 
								<?php
									$parent_category = get_category( ( int ) $category[0]->parent );
									$title = $parent_category->cat_name;
									$link = get_category_link( $parent_category );
									$url = '<a href="' . esc_url( $link ) . '">' . $title . '</a>'; 
								?>

								<?php if( $gantry->get( 'single-meta-category-parent-prefix' ) != '' ) echo $gantry->get( 'single-meta-category-parent-prefix' ); ?>
			
								<?php if( $gantry->get( 'single-meta-category-parent-link', '0' ) ) : ?>
									<?php echo $url; ?>
								<?php else : ?>
									<?php echo $title; ?>
								<?php endif; ?>
							</dd>

						<?php endif; ?>

						<?php /** End Parent Category **/ ?>
	
						<?php /** Begin Category **/ ?>

						<?php if( $gantry->get( 'single-meta-category-enabled', '0' ) && !empty( $category ) ) : ?>

							<dd class="category-name"> 
								<?php 
									$title = $category[0]->cat_name;
									$link = get_category_link( $category[0]->cat_ID );
									$url = '<a href="' . esc_url( $link ) . '">' . $title . '</a>';
								?>

								<?php if( $gantry->get( 'single-meta-category-prefix' ) != '' ) echo $gantry->get( 'single-meta-category-prefix' ); ?>
			
								<?php if( $gantry->get( 'single-meta-category-link', '0' ) ) : ?>
									<?php echo $url; ?>
								<?php else : ?>
									<?php echo $title; ?>
								<?php endif; ?>
							</dd>

						<?php endif; ?>

						<?php /** End Category **/ ?>

						<?php /** Begin Date & Time **/ ?>

						<?php if( $gantry->get( 'single-meta-date-enabled', '1' ) ) : ?>

							<dd class="create"> <?php if( $gantry->get( 'single-meta-date-prefix' ) != '' ) echo $gantry->get( 'single-meta-date-prefix' ) . ' '; ?><?php the_time( $gantry->get( 'single-meta-date-format', 'd F Y' ) ); ?></dd>

						<?php endif; ?>

						<?php /** End Date & Time **/ ?>

						<?php /** Begin Modified Date **/ ?>

						<?php if( $gantry->get( 'single-meta-modified-enabled', '1' ) ) : ?>

							<dd class="modified"> <?php if( $gantry->get( 'single-meta-modified-prefix' ) != '' ) echo $gantry->get( 'single-meta-modified-prefix' ) . ' '; ?><?php the_modified_date( $gantry->get( 'single-meta-modified-format', 'd F Y' ) ); ?></dd>

						<?php endif; ?>

						<?php /** End Modified Date **/ ?>

						<?php /** Begin Author **/ ?>
					
						<?php if( $gantry->get( 'single-meta-author-enabled', '1' ) ) : ?>

							<dd class="createdby"> 
								<?php if( $gantry->get( 'single-meta-author-prefix' ) != '' ) echo $gantry->get( 'single-meta-author-prefix' ) . ' '; ?>

								<?php if( $gantry->get( 'single-meta-author-link', '1' ) ) : ?>
									<?php the_author_posts_link(); ?>
								<?php else : ?>
									<?php the_author(); ?>
								<?php endif; ?>
							</dd>

						<?php endif; ?>

						<?php /** End Author **/ ?>

						<?php /** Begin Comments Count **/ ?>

						<?php if( $gantry->get( 'single-meta-comments-enabled', '1' ) ) : ?>

							<?php if( $gantry->get( 'single-meta-comments-link', '0' ) ) : ?>

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
			
				<?php if( $gantry->get( 'single-featured-image', '1' ) && function_exists( 'the_post_thumbnail' ) && has_post_thumbnail() ) : ?>

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
				
				<?php wp_link_pages( 'before=<div class="pagination page-pagination">' . __( 'Pages:', 'rt_cygnet_wp_lang' ) . '&after=</div>' ); ?>

				<?php edit_post_link( __( 'Edit', 'rt_cygnet_wp_lang' ), '<div class="edit-link">', '</div>' ); ?>

				<?php /** Begin Tags **/ ?>
				
				<?php if( has_tag() && $gantry->get( 'single-tags', '1' ) ) : ?>
																																
					<div class="post-tags">
						<div class="rt-block">
							<div class="module-surround">
								<div class="module-content">
									<?php
									$posttags = get_the_tags();
									foreach ( $posttags as $tag ) { ?>
										<a href="<?php echo get_tag_link( $tag->term_id ); ?>" title="<?php esc_attr_e( $tag->name ); ?>" class="button"><?php echo $tag->name; ?></a>
									<?php }	?>
								</div>
							</div>
						</div>
					</div>

				<?php endif; ?>

				<?php /** End Tags **/ ?>
				
				<?php if( $gantry->get( 'single-footer', '1' ) ) : ?>

					<div class="post-footer">
						<small>
					
						<?php _e( 'This entry was posted', 'rt_cygnet_wp_lang' ); ?>
						<?php /* This is commented, because it requires a little adjusting sometimes.
						You'll need to download this plugin, and follow the instructions:
						http://binarybonsai.com/archives/2004/08/17/time-since-plugin/ */
						/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); echo time_since($entry_datetime); echo ' ago'; */ ?>
						<?php _e( 'on', 'rt_cygnet_wp_lang' ); ?> <?php the_time('l, F jS, Y') ?> <?php _e( 'at', 'rt_cygnet_wp_lang' ); ?> <?php the_time() ?>
						<?php _e( 'and is filed under', 'rt_cygnet_wp_lang' ); ?> <?php the_category(', ') ?>.
						<?php _e( 'You can follow any responses to this entry through the', 'rt_cygnet_wp_lang' ); ?> <?php post_comments_feed_link('RSS 2.0'); ?> <?php _e( 'feed', 'rt_cygnet_wp_lang' ); ?>.

						<?php if (('open' == $post->comment_status) && ('open' == $post->ping_status)) {
						// Both Comments and Pings are open ?>
						<?php _e( 'You can', 'rt_cygnet_wp_lang' ); ?> <a href="#respond"><?php _e( 'leave a response', 'rt_cygnet_wp_lang' ); ?></a>, <?php _e( 'or', 'rt_cygnet_wp_lang' ); ?> <a href="<?php trackback_url(); ?>" rel="trackback"><?php _e( 'trackback', 'rt_cygnet_wp_lang' ); ?></a> <?php _e( 'from your own site.', 'rt_cygnet_wp_lang' ); ?>

						<?php } elseif (!('open' == $post->comment_status) && ('open' == $post->ping_status)) {
						// Only Pings are Open ?>
						<?php _e( 'Responses are currently closed, but you can', 'rt_cygnet_wp_lang' ); ?> <a href="<?php trackback_url(); ?> " rel="trackback"><?php _e( 'trackback', 'rt_cygnet_wp_lang' ); ?></a> <?php _e( 'from your own site.', 'rt_cygnet_wp_lang' ); ?>

						<?php } elseif (('open' == $post->comment_status) && !('open' == $post->ping_status)) {
						// Comments are open, Pings are not ?>
						<?php _e( 'You can skip to the end and leave a response. Pinging is currently not allowed.', 'rt_cygnet_wp_lang' ); ?>

						<?php } elseif (!('open' == $post->comment_status) && !('open' == $post->ping_status)) {
						// Neither Comments, nor Pings are open ?>
						<?php _e( 'Both comments and pings are currently closed.', 'rt_cygnet_wp_lang' ); ?>

						<?php } edit_post_link(__( 'Edit this entry', 'rt_cygnet_wp_lang' ),'','.'); ?>

						</small>
					</div>
													
				<?php endif; ?>

				<?php /** End Post Content **/ ?>

				<?php /** Begin Comments **/ ?>
					
				<?php if( comments_open() && $gantry->get( 'single-comments-form-enabled', '1' ) ) : ?>
															
					<?php echo $gantry->displayComments( true, 'standard', 'standard' ); ?>
				
				<?php endif; ?>

				<?php /** End Comments **/ ?>

			</div>
			
			<?php /** End Post **/ ?>