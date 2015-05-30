<?php
/**
 * @version   $Id: posttype.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 *
 */
class RokGallery_Metaboxes_Posttype
{
    public $defaults;

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * @return array
     */
    function get_defaults()
    {
     $defaults = array(
         //basic
         'gallery_id' => '',
         'default_layout' => 'grid-4col',
         'default_style' => 'light',
         'default_sort_by' => 'gallery_ordering',
         'default_sort_direction' => 'ASC',
         'slice_link_to' => 'default',
         //layout
         'show_sorts' => 1,
         'available_sorts' => array('gallery_ordering','slice_title','slice_updated_at','file_created_at','loves,views'),
         'show_available_layouts' => 1,
         'available_layouts' => array('list-2col','grid-3col','grid-4col'),
         'pages_in_shown_range' => 7,
         'grid-3col-items_per_row' => 3,
         'grid-3col-rows_per_page' => 4,
         'grid-4col-items_per_row' => 4,
         'grid-4col-rows_per_page' => 4,
         'list-2col-items_per_row' => 2,
         'list-2col-rows_per_page' => 6,
         //gallery view
         'gallery_show_title' => 1,
         'gallery_use_title_from' => 'slice',
         'gallery_show_caption' => 1,
         'gallery_use_caption_from' => 'slice',
         'gallery_show_tags' => 1,
         'gallery_use_tags_from' => 'combined',
         'gallery_remove_gallery_tags' => 1,
         'gallery_show_tags_count' => 1,
         'gallery_show_created_at' => 1,
         'gallery_show_loves' => 1,
         'gallery_show_views' => 1,
         //detail view
         'detail_show_title' => 1,
         'detail_use_title_from' => 'slice',
         'detail_show_caption' => 1,
         'detail_use_caption_from' => 'slice',
         'detail_show_tags' => 1,
         'detail_use_tags_from' => 'combined',
         'detail_remove_gallery_tags' => 1,
         'detail_show_tags_count' => 1,
         'detail_show_created_at' => 1,
         'detail_show_updated_at' => 1,
         'detail_show_loves' => 1,
         'detail_show_views' => 1,
         'detail_show_filesize' => 1,
         'detail_use_filesize_from' => 'file',
         'detail_show_dimensions' => 1,
         'detail_use_dimensions_from' => 'file',
         'detail_show_download_full' => 1,
         'detail_show_gallery_info' => 1
     );
     return $defaults;
 }

    /**
     * add the metabox
     */
    function add_metabox()
    {
        if (defined('ROKGALLERY_ERROR_MISSING_LIBS') && ROKGALLERY_ERROR_MISSING_LIBS==true) return;
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        
        RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getUrl('tabs.css'));

        //add_meta_box('rokgallery_metabox', rc__('ROKGALLERY'), array('RokGallery_Metaboxes_Posttype', 'meta_options'), 'post', 'advanced', 'high');
        //add_meta_box('rokgallery_metabox', rc__('ROKGALLERY'), array('RokGallery_Metaboxes_Posttype', 'meta_options'), 'page', 'advanced', 'high');
        add_meta_box('rokgallery_metabox', rc__('ROKGALLERY'), array('RokGallery_Metaboxes_Posttype', 'meta_options'), 'rokgallery', 'normal', 'high');
    }

    //
    /**
     * render metabox contents
     */
    function meta_options()
    {
        global $post;

        //get instance
        $instance = rg_parse_custom_post(get_post_custom($post->ID));//put custom object into an array and get rid of weird arrays of one
        $instance = rg_parse_options($instance, self::get_defaults());//fills in missing values with defaults
        $instance = rg_parse_options($instance, get_post($post->ID, ARRAY_A));//adds post data without overwriting custom fields

        ob_start();
        echo RokCommon_Composite::get('rg_forms')->load('posttype_form.php', array('instance' => $instance));
        echo ob_get_clean();
    }
}
add_action('add_meta_boxes', array('RokGallery_Metaboxes_Posttype', 'add_metabox'));