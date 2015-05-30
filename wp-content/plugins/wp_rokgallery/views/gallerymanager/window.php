<?php
 /**
 * @version   $Id: window.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

global $wpdb;

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>RokGallery</title>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>"/>
    <?php wp_head(); ?>
    <base target="_self"/>
</head>
<body>
    <?php echo $content; ?>
	<?php wp_footer(); ?>
</body>
</html>