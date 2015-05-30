<?php
/**
 * @version   $Id: gallerypicker.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 *
 */
class RokGallery_Metaboxes_GalleryPicker
{

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * add the metabox
     */
    function add_metabox()
    {
        if (defined('ROKGALLERY_ERROR_MISSING_LIBS') && ROKGALLERY_ERROR_MISSING_LIBS==true) return;
        //add thickbox
        wp_enqueue_script('jquery');
        wp_enqueue_style('thickbox');
        add_thickbox();

        add_meta_box('rokgallery_metabox', rc__('ROKGALLERY'), array('RokGallery_Metaboxes_GalleryPicker', 'meta_options'), 'post', 'side', 'high');
        add_meta_box('rokgallery_metabox', rc__('ROKGALLERY'), array('RokGallery_Metaboxes_GalleryPicker', 'meta_options'), 'page', 'side', 'high');
        //add_meta_box('rokgallery_metabox', rc__('ROKGALLERY'), array('RokGallery_Metaboxes_GalleryPicker', 'meta_options'), 'rokgallery', 'side', 'high');
    }

    //
    /**
     * render metabox contents
     */
    function meta_options()
    {
        $nonce = wp_create_nonce('rokgallery-ajax-nonce');

        $link = get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action=rokgallery_gallerypicker&nonce='.$nonce.'&TB_iframe=true&height=555&width=425&modal=false';

        echo '<div><a id="gallery-popup" class="thickbox button" style="float:left;" title="Select a Gallery"  href="' . $link . '">Insert RokGallery</a></div><div style="clear:both;"></div>' . "\n";
    }
}
add_action('add_meta_boxes', array('RokGallery_Metaboxes_GalleryPicker', 'add_metabox'));