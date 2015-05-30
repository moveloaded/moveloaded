<?php
/**
 * @version   2.50.5 November 20, 2014
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * No Direct Access
 */
defined( 'ABSPATH' ) or die( 'Restricted access' );

global $rokbox;

// add thickbox
wp_enqueue_style( 'thickbox' );
wp_enqueue_script( 'jquery' );
add_thickbox();
?>
<!doctype html>
<html>
	<head>
		<title>RokBox <?php _e( 'Snippets Generator', 'rokbox' ); ?></title>
		<link rel="stylesheet" href="<?php echo $rokbox->_url; ?>/tinymce/css/rokbox.css"></link>
		<script src="<?php echo $rokbox->_url; ?>/assets/js/mootools.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $rokbox->_url; ?>/tinymce/js/rokbox.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo get_bloginfo( 'wpurl' ); ?>/wp-includes/js/tinymce/tiny_mce_popup.js" type="text/javascript" charset="utf-8"></script>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<span class="label"><?php _e( 'Link', 'rokbox' ); ?><span class="required-input">*</span></span>
				<input id="link" name="link" data-required type="text" placeholder="ie. http://your-site.com/wp-content/uploads/my_image.png"></input>
			</div>
			<div class="row">
				<span class="label"><?php _e( 'DOM Element', 'rokbox' ); ?></span>
				<input id="element" name="element" type="text" placeholder="ie, body form#form-login // div.some-class-name"></input>
				<div class="notice"><?php _e( 'Specify a CSS rule that matches the element in the page you want to render in the popup.', 'rokbox' ); ?></div>
			</div>
			<div class="row">
				<span class="label"><?php _e( 'Album', 'rokbox' ); ?></span>
				<input name="album" type="text" placeholder="RokBox, Gallery, Personal, etc..."></input>
			</div>
			<div class="row">
				<span class="label"><?php _e( 'Caption', 'rokbox' ); ?></span>
				<input name="caption" type="text" placeholder=""></input>
			</div>
			<div class="row">
				<span class="label"><?php _e( 'Content', 'rokbox' ); ?></span>
				<label for="text" class="radio">
					<input id="text" data-switcher name="content" type="radio" value="text" checked></input>
					<?php _e( 'Text', 'rokbox' ); ?>
				</label>
				<label for="thumbnail" class="radio">
					<input id="thumbnail" data-switcher name="content" type="radio" value="thumbnail"></input>
					<?php _e( 'Thumbnail', 'rokbox' ); ?>
				</label>
				<div class="sub-row">
					<input class="text_text" id="text_text" name="text" type="text" placeholder="ie. My RokBox"></input>
					<?php if( isset( $_GET['type'] ) && $_GET['type'] == 'tinymce' ) { ?>
						<div class="notice text_text"><?php _e( 'Leave the field blank to wrap your current selection in the Editor', 'rokbox' ); ?></div>
					<?php } ?>

					<input class="thumbnail_text" id="thumbnail_text" name="thumbnail" type="text" placeholder="ie. http://your-site.com/wp-content/uploads/my_image.png"></input>
					<div class="notice thumbnail_text"><?php _e( 'Leave the field blank to auto-generate thumbnails if the Link is a local image placed inside of your WordPress directory.', 'rokbox' ); ?></div>
				</div>
			</div>

			<div class="footer">
				<ul>
					<li><a href="#" id="button-insert-new"><?php _e( 'Insert and New', 'rokbox' ); ?></a></li>
					<li><a href="#" id="button-insert-close"><?php _e( 'Insert and Close', 'rokbox' ); ?></a></li>
					<li><a href="#" id="button-cancel"><?php _e( 'Cancel', 'rokbox' ); ?></a></li>
				</ul>
			</div>
		</div>
	</body>
</html>
