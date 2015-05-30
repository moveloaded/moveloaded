<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined( 'GANTRY_VERSION' ) or die();

gantry_import( 'core.gantrylayout' );

/**
 *
 * @package gantry
 * @subpackage html.layouts
 */
class GantryLayoutComment_Standard extends GantryLayout {
	var $render_params = array(
		'comment' => null,
		'depth' => 0,
		'args' => array()
	);

	function render( $params = array() ) {
		global $gantry;
		$fparams = $this->_getParams( $params );
	}

	function render_comment( $comment, $args, $depth ) {
		global $post;

		ob_start();

		$GLOBALS['comment'] = $comment;
		
		$avatar = get_avatar( $comment, $size = 54 );
		$avatar = str_replace( "class='", "class='rt-image ", $avatar );

		?>
		
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<div id="comment-<?php comment_ID(); ?>" class="comment-body comment">

				<div class="author-avatar">
					<?php echo $avatar; ?>
				</div>

				<div class="comment-meta comment-author vcard">
					<?php
						printf(
							'<span class="author lead">%s</span>', 
							get_comment_author_link()
						);
					?>

					<?php
						if ( $post = get_post( $post_id ) ) {
							if ( $comment->user_id === $post->post_author )
								echo '<div class="author-badge button">' . __( 'Author', 'rt_cygnet_wp_lang' ) . '</div>';
						}
					?>
					<br />
					<small>
						<?php 
							// translators %1$s date, %2$s time
							echo sprintf(
								'<a href="%1$s" class="comment-time"><cite>%2$s</cite></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								sprintf( __( '%1$s at %2$s', 'rt_cygnet_wp_lang' ), 
								get_comment_date(),
								get_comment_time() )
							);
						?>
					</small>
				</div>

				<div class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'reply_text' => '<i class="fa fa-reply"></i>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div>

				<?php if ( $comment->comment_approved == '0' ) : ?>
				<p class="info">
					<span class="awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'rt_cygnet_wp_lang' ); ?></span>
				</p>
				<?php endif; ?>

				<div class="comment-content">

					<?php comment_text(); ?>

					<?php edit_comment_link( __( 'Edit', 'rt_cygnet_wp_lang' ), '<p class="edit-link">', '</p>' ); ?>
				</div>

			</div>
		 
		<?php

		unset($comment_classes, $get_comment_classes);

		echo ob_get_clean();

		return;
	}
}