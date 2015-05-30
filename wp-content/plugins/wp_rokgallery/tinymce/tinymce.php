<?php
/**
 * @version $Id: tinymce.php 10867 2013-05-30 04:04:46Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_TinyMCE
{

    /**
     * @var string
     */
    var $pluginname = 'RokGallery';
    /**
     * @var string
     */
    var $path = '';
    /**
     * @var int
     */
    var $internalVersion = 200;

    /**
     * Class Constructor
     */
    function __construct()
    {
    }

    /**
     * @return mixed
     */
    public static function addbuttons()
    {
        if (defined('ROKGALLERY_ERROR_MISSING_LIBS') && ROKGALLERY_ERROR_MISSING_LIBS==true) return;
        // Don't bother doing this stuff if the current user lacks permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
            return;

        // Add only in Rich Editor mode
        if (get_user_option('rich_editing') == 'true') {
            add_filter("mce_external_plugins", array('RokGallery_TinyMCE', "add_tinymce_plugin"));
            add_filter('mce_buttons', array('RokGallery_TinyMCE', 'register_button'));
        }
    }

    /**
     * @param $buttons
     * @return array
     */
    public static function register_button($buttons)
    {
        array_push($buttons, "separator", "rokgallery");
        return $buttons;
    }

    // Load the TinyMCE plugin : editor_plugin.js (wp2.5)
    /**
     * @param $plugin_array
     * @return array
     */
    public static function add_tinymce_plugin($plugin_array)
    {
        $plugin_array['rokgallery'] = ROKGALLERY_PLUGIN_URL.'/tinymce/editor_plugin.js';
        return $plugin_array;
    }

//    function get_defaults()
//    {
//     $defaults = array(
//         //basic
//         'gallery_id' => '',
//         'default_layout' => 'grid-4col',
//         'default_style' => 'light',
//         'default_sort_by' => 'gallery_ordering',
//         'default_sort_direction' => 'ASC',
//         'slice_link_to' => 'default',
//         //layout
//         'show_sorts' => 1,
//         'available_sorts' => 'gallery_ordering,slice_title,slice_updated_at,file_created_at,loves,views',
//         'show_available_layouts' => 1,
//         'available_layouts' => 'list-2col,grid-3col,grid-4col',
//         'pages_in_shown_range' => 7,
//         'grid-3col-items_per_row' => 3,
//         'grid-3col-rows_per_page' => 4,
//         'grid-4col-items_per_row' => 4,
//         'grid-4col-rows_per_page' => 4,
//         'list-2col-items_per_row' => 2,
//         'list-2col-rows_per_page' => 6,
//         //gallery view
//         'gallery_show_title' => 1,
//         'gallery_use_title_from' => 'slice',
//         'gallery_show_caption' => 1,
//         'gallery_use_caption_from' => 'slice',
//         'gallery_show_tags' => 1,
//         'gallery_use_tags_from' => 'combined',
//         'gallery_remove_gallery_tags' => 1,
//         'gallery_show_tags_count' => 1,
//         'gallery_show_created_at' => 1,
//         'gallery_show_loves' => 1,
//         'gallery_show_views' => 1,
//         //detail view
//         'detail_show_title' => 1,
//         'detail_use_title_from' => 'slice',
//         'detail_show_caption' => 1,
//         'detail_use_caption_from' => 'slice',
//         'detail_show_tags' => 1,
//         'detail_use_tags_from' => 'combined',
//         'detail_remove_gallery_tags' => 1,
//         'detail_show_tags_count' => 1,
//         'detail_show_created_at' => 1,
//         'detail_show_updated_at' => 1,
//         'detail_show_loves' => 1,
//         'detail_show_views' => 1,
//         'detail_show_filesize' => 1,
//         'detail_use_filesize_from' => 'file',
//         'detail_show_dimensions' => 1,
//         'detail_use_dimensions_from' => 'file',
//         'detail_show_download_full' => 1,
//         'detail_show_gallery_info' => 1
//
//     );
//     return $defaults;
// }
//
//
    /**
     * Call TinyMCE window content via admin-ajax
     * there is no iframe handler in WP so we use ajax
     */
    public static function rokgallery_tinymce_ajax()
    {
        global $wpdb;

        //        $nonce = RokGallery_Request::getString('nonce');
        //        if (!wp_verify_nonce($nonce, 'rokgallery-ajax-nonce')) {
        //            return;
        //        }

        $instance = new stdClass();
        $instance->action = RokGallery_Request::getstring('action', 'rokgallery_tinymce', 'get');
        $instance->textarea = RokGallery_Request::getString('textarea', 'content', 'get');
        $instance->inputfield = RokGallery_Request::getString('inputfield', '', 'get');
        $instance->show_menuitems = RokGallery_Request::getInt('show_menuitems', 1, 'post');
        $instance->gallery_id = RokGallery_Request::getInt('gallery_id', 0, 'post');
        $instance->file_id = RokGallery_Request::getInt('file_id', 0, 'post');
        $instance->page = RokGallery_Request::getInt('page', 1, 'request');


        //get view
        $view = new RokGallery_Views_GalleryPicker_View();
        $view = $view->getView($instance);

        RokCommon_Header::addScript(site_url().'/wp-includes/js/tinymce/tiny_mce_popup.js');
        //        RokCommon_Header::addScript(site_url().'/wp-includes/js/tinymce/mctabs.js');
        //        RokCommon_Header::addScript(site_url().'/wp-includes/js/tinymce/form_utils.js');

        //        RokCommon_Header::addScript(RokCommon_Composite::get('rokgallery.gallerypicker')->loadAll('includes.php', array('that'=>$view)));

        RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getUrl('Scrollbar.js'));
        RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getUrl('MainPage.js'));
        RokCommon_Header::addScript(RokCommon_Composite::get('rokgallery.gallerypicker')->getUrl('gallerypicker.js'));
        RokCommon_Header::addStyle(RokCommon_Composite::get('rokgallery.gallerypicker')->getUrl('gallerypicker.css'));

        //render popup content
        ob_start();
        if ($view->inline_js) {
            RokCommon_Header::addInlineScript($view->inline_js);
        }
        if ($view->inline_css) {
            RokCommon_Header::addInlineStyle($view->inline_css);
        }
        RokCommon_Header::addInlineScript(RokCommon_Composite::get($view->context)->load('javascript.php', array('that' => $view)));
        echo RokCommon_Composite::get('rg_views.gallerypicker')->load('default.php', array('view' => $view));
        $content = ob_get_contents();
        ob_end_clean();

        ob_start();
        echo RokCommon_Composite::get('rg_tinymce')->load('window.php', array('content' => $content));
        $content = ob_get_contents();
        ob_end_clean();

        echo $content;
        // IMPORTANT: don't forget to "exit"
        exit;
    }

    /**
     * Adds RokGallery QuickTag Buttons
     */
    public static function rokgallery_quicktags()
    {
        global $pagenow, $wp_version;

        //add thickbox
        wp_enqueue_script('jquery');
        wp_enqueue_style('thickbox');
        wp_enqueue_script('quicktags');
        add_thickbox();

        if (version_compare($wp_version, '3.1.0', '<')) {
        wp_enqueue_script('my_custom_quicktags', plugin_dir_url(__FILE__).'quicktags.js', array('quicktags'));

        } else {

        ?>
        <script type ="text/javascript" >
            if ( typeof(QTags) == 'function' ) {

                QTags.addButton('rokgallery', 'rokgallery', '', '');

                // the rokgallery button

                if (typeof MooTools != 'undefined'){
                window.addEvent('domready', function ()
                {
                    if($('qt_content_rokgallery')) {
                        $('qt_content_rokgallery').addEvent('mouseover', function ()
                        {
                            if ($('rokgallery_link') == null) {
                                var
                                url = ajaxurl + '?action=rokgallery_gallerypicker&TB_iframe=true&height=600&width=555&modal=false';
                                var
                                newLinknew = Element('a#rokgallery_link').wraps(this);
                                $('rokgallery_link').setProperty('href', url);
                                $('rokgallery_link').setProperty('class', 'thickbox');
                            }
                        })
                    }
                });
                }
            }
        </script>

        <?php
        }
    }
}

add_action('init', array('RokGallery_TinyMCE', 'addbuttons'));
add_action('wp_ajax_rokgallery_tinymce', array('RokGallery_TinyMCE', 'rokgallery_tinymce_ajax'));
add_action('wp_ajax_nopriv_rokgallery_tinymce', array('RokGallery_TinyMCE', 'rokgallery_tinymce_ajax'));
add_action('admin_print_footer_scripts', array('RokGallery_TinyMCE', 'rokgallery_quicktags'));
