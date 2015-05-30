<?php
/**
 * @version   $Id: rokgallery.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 *
 */
class RokGallery_Posttypes_RokGallery
{
    /**
     * class constructor
     */
    function __construct() {}

    /**
     * @return array
     */
    public static function get_defaults()
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
            'available_sorts' => array('gallery_ordering', 'slice_title', 'slice_updated_at', 'file_created_at', 'loves,views'),
            'show_available_layouts' => 1,
            'available_layouts' => array('list-2col', 'grid-3col', 'grid-4col'),
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
     * initialize rokgallery posttype
     */
    public static function init()
    {
        if (defined('ROKGALLERY_ERROR_MISSING_LIBS') && ROKGALLERY_ERROR_MISSING_LIBS==true) return;
        //if rokcommon and rokgallery isn't installed and activated we don't show menu or run posttype
        if (!rg_rokcommon_check() || !rg_rokgallery_check() || !rg_db_check()) { return; }

        $labels = array(
            'name' => _x('RokGallery', 'post type general name'),
            'singular_name' => _x('RokGallery', 'post type singular name'),
            'add_new' => _x('Add New', 'rokgallery'),
            'add_new_item' => rc__('Add New Gallery'),
            'edit_item' => rc__('Edit Gallery'),
            'new_item' => rc__('New Galleries'),
            'all_items' => rc__('All Galleries'),
            'view_item' => rc__('View Gallery'),
            'search_items' => rc__('Search Galleries'),
            'not_found' => rc__('No Galleries Found'),
            'not_found_in_trash' => rc__('No Galleries Found in Trash'),
            'parent_item_colon' => '',
            'menu_name' => 'RokGallery Pages'
        );
        $args = array(
            'labels' => $labels,
            'description' => rc__('ROKGALLERY_POSTTYPE_DESC'),
            'show_ui' => true, // UI in admin panel
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => ROKGALLERY_PLUGIN_URL . '/assets/images/rokgallery_16x16.png',
            'public' => true,
            '_edit_link' => 'post.php?post=%d',
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array("slug" => "gallery"), // Permalinks format replaces rokgallery with gallery in url
            'query_var' => 'rokgallery', // This goes to the WP_Query schema
            'supports' => array('title', 'thumbnail'),
            'show_in_nav_menus' => true,
            'has_archive' => true
        );
        register_post_type('rokgallery', $args);

        //load translator
        load_plugin_textdomain('wp_rokgallery', false, ROKGALLERY_PLUGIN_REL_PATH.'/languages');
        $container = RokCommon_Service::getContainer();
        /** @var $i18n RokCommon_I18N_Wordpress */
        $i18n = $container->i18n;
        $i18n->addDomain('wp_rokgallery');
    }

    /**
     * this is better than template_redirect or shortcode
     * @param $content
     * @return string
     */
    public static function filter_content($content)
    {

        global $post, $wp;

        if ($wp->query_vars['post_type'] == 'rokgallery') {

            $tmpl = RokGallery_Request::getString('view', 'gallery');
            $className = 'RokGallery_Views_' . ucfirst($tmpl) . '_View';

            //get instance
            $instance = rg_parse_custom_post(get_post_custom($post->ID)); //put custom object into an array and get rid of weird arrays of one
            $instance = rg_parse_options($instance, self::get_defaults()); //fills in missing values with defaults
            $instance = rg_parse_options($instance, get_post($post->ID, ARRAY_A)); //adds post data without overwriting custom fields

            //set up view
            $rokgallery_view = new $className;
            $view = $rokgallery_view->getView($instance);

            //TODO these are included by the template files, but that is too late for the WP Header
            RokCommon_Header::addStyle(RokCommon_Composite::get($view->context)->getUrl($tmpl . '.css'));
            RokCommon_Header::addStyle(RokCommon_Composite::get($view->style_context)->getUrl('style.css'));
            RokCommon_Header::addInlineScript(RokCommon_Composite::get('rokgallery')->load('js-settings.php', array('that' => $view)));

            $browser = new RokCommon_Browser();
            if ($browser->getShortName() == 'ie7') {
                RokCommon_Header::addStyle(RokCommon_Composite::get('rokgallery')->getUrl('rokgallery-ie7.css'));
            }
            RokCommon_Header::addScript(RokCommon_Composite::get('rokgallery')->getUrl('loves' . RokGallery_Helper::getJSVersion() . '.js'));

            ob_start();
            echo RokCommon_Composite::get('rg_views.' . $tmpl)->load('default.php', array('view' => $view));
            $new_content = ob_get_contents();
            ob_end_clean();

            return $content . '<br />' . $new_content;
        } else {
            return $content;
        }
    }

    /**
     * save custom metabox fields with save_post hook
     * @param $post_id
     * @param null $post
     */
    public static function save_post($post_id, $post = null)
    {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
             return;
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
             return;

        if ($post->post_type == "rokgallery") {
            foreach (self::get_defaults() as $key => $value)
            {
                $value = RokGallery_Request::getRaw($key, 'null', 'post');
                if (is_null($value)) {
                    delete_post_meta($post_id, $key);
                    continue;
                }
                if (!is_array($value)) {
                    if (!update_post_meta($post_id, $key, $value)) {
                        add_post_meta($post_id, $key, $value, true);
                    }
                }
                else {
                    delete_post_meta($post_id, $key);
                    foreach ($value as $entry) {
                        add_post_meta($post_id, $key, $entry, false);
                    }
                }
            }
        }
    }

    /**
     * @param $data
     * @param $postarr
     * @return array
     */
    public static function filter_post_data($data , $postarr){
        $data['guid'] = html_entity_decode($data['guid']);
        return $data;
    }


    // Add a Custom Post Type to a feed
    /**
     * @param $qv
     * @return array
     */
    public static function add_to_feed($qv)
    {
        if (isset($qv['feed']) && !isset($qv['post_type']))
            $qv['post_type'] = array('post', '<ROKGALLERY>');
        return $qv;
    }

    /**
     * flush_rules() if our rules are not yet included
     */
    //TODO this is dirty, needs a cleaner way of detection
    /**
     *
     */
    public static function flush_rules()
    {

        $rules = get_option('rewrite_rules');

        if (!isset($rules['(project)/(\d*)$'])) {
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
        }
    }

    /**
     * Adding a new rule
     * @param $rules
     * @return array
     */
    public static function insert_rewrite_rules($rules)
    {
        $newrules = array();
        $newrules['gallery/([0-9]{1,})/?$'] = 'index.php?post_type=rokgallery&rokgallery=$matches[1]&';
        return $newrules + $rules;
    }

    /**
     * Adding these vars so that WP recognizes them
     * @param $vars
     * @return array
     */
    public static function insert_query_vars($vars)
    {
        $vars = array_unique(array_merge($vars, array('gallery', 'layout', 'sort_by', 'sort_direction', 'page', 'view', 'id')));
        return $vars;
    }
}

/**
 * ATTENTION: This is *only* done during plugin activation hook!
 * You should *NEVER EVER* do this on every page load!!
 */
if (!function_exists('rokgallery_rewrite_flush')) {

    /**
     *
     */
    function rokgallery_rewrite_flush()
    {
        RokGallery_Posttypes_RokGallery::init();
        flush_rewrite_rules();
    }
}


add_action('init', array('RokGallery_Posttypes_RokGallery', 'init'));
add_action('save_post', array('RokGallery_Posttypes_RokGallery', 'save_post'), 10, 2);
add_filter('wp_insert_post_data',array('RokGallery_Posttypes_RokGallery', 'filter_post_data'), 99, 2 );
add_filter('the_content', array('RokGallery_Posttypes_RokGallery', 'filter_content'));
add_filter('request', array('RokGallery_Posttypes_RokGallery', 'add_to_feed'));
add_filter('rewrite_rules_array', array('RokGallery_Posttypes_RokGallery', 'insert_rewrite_rules'));
add_filter('query_vars', array('RokGallery_Posttypes_RokGallery', 'insert_query_vars'));
add_action('wp_loaded', array('RokGallery_Posttypes_RokGallery', 'flush_rules'));

register_activation_hook(ROKGALLERY_PLUGIN_FILE, 'rokgallery_rewrite_flush');