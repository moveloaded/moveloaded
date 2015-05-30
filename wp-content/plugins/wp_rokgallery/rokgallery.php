<?php
/**
 * @version   $Id: rokgallery.php 21683 2014-06-23 13:00:34Z jakub $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
/*
Plugin Name: RokGallery
Plugin URI: http://www.rockettheme.com
Description: A gallery component for Wordpress from RocketTheme.
Author: RocketTheme, LLC
Version: 2.31
Author URI: http://www.rockettheme.com
License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/
if (!defined('ROKGALLERY_NEEDED_ROKCOMMON_VERSION')) define('ROKGALLERY_NEEDED_ROKCOMMON_VERSION','3.1.6');

//RokGallery Globals
if (is_multisite()) $plugin = 'wp_rokgallery/rokgallery.php';

//Defines
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if (!defined('ROKGALLERY_DEV')) define('ROKGALLERY_DEV', false);
if (!defined('ROKGALLERY_VERSION')) define('ROKGALLERY_VERSION', '2.31');
if (!defined('ROKGALLERY_PLUGIN_FILE')) define('ROKGALLERY_PLUGIN_FILE', str_replace(WP_PLUGIN_DIR, '', $plugin));
if (!defined('ROKGALLERY_PLUGIN_REL_PATH')) define('ROKGALLERY_PLUGIN_REL_PATH', '/' . str_replace(array('/', '\\'), '', dirname(ROKGALLERY_PLUGIN_FILE)));
if (!defined('ROKGALLERY_PLUGIN_PATH')) define('ROKGALLERY_PLUGIN_PATH', WP_PLUGIN_DIR . ROKGALLERY_PLUGIN_REL_PATH);
if (!defined('ROKGALLERY_PLUGIN_URL')) define('ROKGALLERY_PLUGIN_URL', WP_PLUGIN_URL . ROKGALLERY_PLUGIN_REL_PATH);

//load required files in required order
include_once(ROKGALLERY_PLUGIN_PATH . '/functions.php');
include_once(ROKGALLERY_PLUGIN_PATH . '/include.php');
include_once(ROKGALLERY_PLUGIN_PATH . '/request.php');

if (!preg_match('/project.version/', ROKCOMMON) && version_compare(preg_replace('/-SNAPSHOT/', '', ROKCOMMON), ROKGALLERY_NEEDED_ROKCOMMON_VERSION, '<')) {
    rg_force_deactivate();
    rokgallery_set_admin_message('error', __('RokGallery v2.31 needs at least RokCommon v'.ROKGALLERY_NEEDED_ROKCOMMON_VERSION.'.  You currently have RokCommon v' . ROKCOMMON));
    return;
}
include_once(ROKGALLERY_PLUGIN_PATH . '/widgets/rokgallery.php');
include_once(ROKGALLERY_PLUGIN_PATH . '/posttypes/rokgallery.php');
include_once(ROKGALLERY_PLUGIN_PATH . '/metaboxes/posttype.php');
//include_once(ROKGALLERY_PLUGIN_PATH . '/metaboxes/gallerypicker.php');
include_once(ROKGALLERY_PLUGIN_PATH . '/tinymce/tinymce.php');

// Define Cache Dir
if (!defined('ROKGALLERY_CACHE')) {
    define('ROKGALLERY_CACHE', ROKGALLERY_PLUGIN_PATH . '/cache');
}

if (!class_exists('RokGallery_Plugin')) {
    /**
     *
     */
    class RokGallery_Plugin
    {
        /**
         * class constructor
         */
        function __construct()
        {
        }

        /**
         * @return array
         */
        public static function get_defaults()
        {
            $defaults = array(
                'allow_duplicate_files' => 1,
                'publish_slices_on_file_publish' => 0,
                'gallery_remove_slice' => 1,
                'gallery_autopublish' => 1,
                'default_thumb_xsize' => 150,
                'default_thumb_ysize' => 150,
                'default_thumb_keep_aspect' => 1,
                'default_thumb_background' => '#000',
                'jpeg_quality' => 80,
                'png_compression' => 0,
                'love_text' => 'Love',
                'unlove_text' => 'Unlove'
            );
            add_option('rokgallery_plugin_settings', $defaults);
            return $defaults;
        }


        /**
         * initialize rokgallery plugin
         */
        public static function init()
        {
            if (defined('ROKGALLERY_ERROR_MISSING_LIBS') && ROKGALLERY_ERROR_MISSING_LIBS==true) return;
            //if rokcommon isn't installed and/or activated we deactivate rokgallery
            if (!rg_rokcommon_check()) {
                return;
            }

            //register plugin settings
            register_setting('rokgallery_settings_group', 'rokgallery_plugin_settings');

            //load translator
            load_plugin_textdomain('wp_rokgallery', false, ROKGALLERY_PLUGIN_REL_PATH . '/languages');
            $container = RokCommon_Service::getContainer();
            /** @var $i18n RokCommon_I18N_Wordpress */
            $i18n = $container->i18n;
            $i18n->addDomain('wp_rokgallery');

            //we need these on all rokgallery admin pages
            RokCommon_Header::addStyle(ROKGALLERY_PLUGIN_URL . '/rokgallery.css');

            //we need these in the options pages
            if (RokGallery_Request::getString('page') == 'rokgallery-options') {
                RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('buttons.css'));
                RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.moorainbow')->getUrl('mooRainbow.css'));
                RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.moorainbow')->getUrl('mooRainbow.js'));
            }

            //we only need these in the default page
            //add thickbox
            wp_enqueue_script('jquery');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('quicktags');
            add_thickbox();
            if (RokGallery_Request::getString('page') == 'rokgallery-default') {
                RokCommon_Composite::get('rokgallery.default')->load('includes.php', array());
            }

        }

        /**
         * add rokgallery admin menu
         */
        public static function admin_menu()
        {
            if (defined('ROKGALLERY_ERROR_MISSING_LIBS') && ROKGALLERY_ERROR_MISSING_LIBS==true) return;
            //if rokcommon and rokgallery isn't installed and activated we don't show menu
            if (!rg_rokcommon_check() || !rg_rokgallery_check() || !rg_db_check()) {
                return;
            }

            add_menu_page('RokGallery Administration', 'RokGallery Admin', 'manage_options', 'rokgallery-default', array(
                'RokGallery_Plugin',
                'default_view'
            ), ROKGALLERY_PLUGIN_URL . '/assets/images/rokgallery_16x16.png');
            add_submenu_page('rokgallery-default', 'RokGallery Settings', 'Settings', 'manage_options', 'rokgallery-options', array(
                'RokGallery_Plugin',
                'settings_view'
            ));
        }

        /**
         * render rokgallery plugin default view
         */
        public static function default_view()
        {
            $view = new RokGallery_Views_Default_View();
            $view = $view->getView();

            ob_start();
            RokCommon_Header::addInlineScript($view->inline_js);
            echo RokCommon_Composite::get('rg_views.default')->load('default.php', array('view' => $view));
            echo ob_get_clean();

        }

        /**
         * render rokgallery plugin settings view
         */
        public static function settings_view()
        {
            $instance = rg_parse_options(get_option('rokgallery_plugin_settings'), RokGallery_Plugin::get_defaults());
            $instance['admin_link'] = 'admin.php?page=rokgallery-default';

            ob_start();
            echo RokCommon_Composite::get('rg_forms')->load('plugin_form.php', array('instance' => $instance));
            echo ob_get_clean();
        }
    }
}

if (!function_exists('rokgallery_activate')) {
    /**
     * function runs on plugin initial activation
     */
    function rokgallery_activate()
    {
        if (!rg_rokcommon_check()) {
            return;
        }

        $include_file = ROKGALLERY_PLUGIN_PATH . '/lib/requirements.php';
        $included_files = get_included_files();
        if (!in_array($include_file, $included_files) && ($loaderrors = include_once($include_file)) !== true) {
            rg_force_deactivate();
            rokgallery_set_admin_message('error', __('<strong>Unable to activate RokGallery:</strong><br/>') . implode("<br/>", $loaderrors));
        }

        $file = ROKGALLERY_PLUGIN_PATH . '/install/install.mysql.utf8.sql';

        $result = rokgallery_run_sql($file);

        if ($result['complete']) {
            rokgallery_set_admin_message('updated', __('RokGallery database installation <strong>Successful!</strong>'));
        }
        if (!empty($result['errors'])) {
            rokgallery_set_admin_message('error', __('RokGallery database installation encountered errors:<br/>' . implode("<br/>", $result['errors'])));
        }

        $dir = WP_CONTENT_DIR . '/uploads/rokgallery/';
        if (!is_dir($dir)) {
            if (mkdir($dir, 0777, true)) {
                rokgallery_set_admin_message('updated', __('RokGallery upload folder creation <strong>Successful!</strong>'));
            } else {
                rokgallery_set_admin_message('error', __('RokGallery upload folder creation <strong>Failed!</strong>'));
            }
        }

        $container_configs = get_site_option('rokcommon_configs', array());
        if (!array_key_exists('rokgallery_container', $container_configs) || !array_key_exists('rokgallery_library', $container_configs)) {
            $container_config = array();
            $container_config['file'] = '/wp-content/plugins/' . ROKGALLERY_PLUGIN_REL_PATH . '/container.xml';
            $container_config['extension'] = 'rokgallery';
            $container_config['priority'] = 10;
            $container_config['type'] = 'container';
            $container_configs['rokgallery_container'] = $container_config;

            $library_config = array();
            $library_config['file'] = '/wp-content/plugins/' . ROKGALLERY_PLUGIN_REL_PATH . '/lib';
            $library_config['extension'] = 'rokgallery';
            $library_config['priority'] = 10;
            $library_config['type'] = 'library';
            $container_configs['rokgallery_library'] = $library_config;
            update_site_option('rokcommon_configs', $container_configs);
        }

        //RokGallery_Plugin::get_defaults(); //set plugin defaults in db
        //RokGallery_Widgets_RokGallery::get_defaults(); //set widget defaults in db
    }
}

if (!function_exists('rokgallery_uninstall')) {
    /**
     * function runs on plugin uninstall
     */
    function rokgallery_uninstall()
    {
        global $wpdb;
        $isNetwork = (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'network') !== false) ? true : false;
        $isUninstall = (isset($_GET['action']) && $_GET['action'] == 'uninstall') ? true : false;

        $file = ROKGALLERY_PLUGIN_URL . '/install/uninstall.mysql.utf8.sql';

        $result = rokgallery_run_sql($file);

        if ($result[0]) {
            rokgallery_set_admin_message('updated', __('Removal of RokGallery database tables was <strong>Successful!</strong>'));
        }

        if ((is_multisite() && $isNetwork && $isUninstall) || (!is_multisite())) {
            delete_option('rokgallery_plugin_settings');
            delete_option('widget_rokgallery_options');
        }

        $dir = WP_CONTENT_DIR . '/uploads/rokgallery/';
        if (!is_dir($dir)) {
            if (rmdir($dir)) {
                rokgallery_set_admin_message('updated', __('RokGallery upload folder removal <strong>Successful!</strong>'));
            } else {
                rokgallery_set_admin_message('error', __('RokGallery upload folder removal <strong>Failed!</strong>'));
            }
        }

        $container_configs = get_site_option('rokcommon_configs', array());
        if (array_key_exists('rokgallery_container', $container_configs) || array_key_exists('rokgallery_library', $container_configs)) {
            unset($container_configs['rokgallery_container']);
            unset($container_configs['rokgallery_library']);
            update_site_option('rokcommon_configs', $container_configs);
        }
    }
}

if (!function_exists('rokgallery_run_sql')) {
    /**
     * @param string $file
     *
     * @return bool
     */
    function rokgallery_run_sql($file)
    {
        global $wpdb;
        $isNetwork = (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'network') !== false) ? true : false;
        $isActivation = (isset($_GET['action']) && $_GET['action'] == 'activate') ? true : false;

        $complete = true;
        $sql = file($file);
        $new_sql = '';
        foreach ($sql as $sql_line) {
            if (trim($sql_line) != "" && strpos($sql_line, "--") === false) {
                $new_sql .= $sql_line;
            }
        }
        $queries = explode(';', $new_sql);

        $new_queries = array();
        //multisite queries
        if (is_multisite() && $isNetwork && $isActivation) {
            $old_blog = $wpdb->blogid;
            $blog_ids = $wpdb->get_col('SELECT blog_id FROM ' . $wpdb->blogs);
            foreach ($blog_ids as $blog_id) {

                switch_to_blog($blog_id);
                foreach ($queries as $query) {
                    $query = str_replace('#__', $wpdb->prefix, $query); //add wp db prefix + blog id

                    if (trim($query) != "" && strpos($query, "--") === false) {
                        $new_queries[] = trim($query); //trim again
                    }
                }
            }
            //go back to blog we started with
            switch_to_blog($old_blog);

        } else {
            foreach ($queries as $query) {
                //run main query
                $query = str_replace('#__', $wpdb->prefix, $query); //add wp db prefix
                if (trim($query) != "" && strpos($query, "--") === false) {
                    $new_queries[] = trim($query); //trim again
                }
            }
        }

        $errors = array();
        $failed_queries = array();
        $successful_queries = array();
        foreach ($new_queries as $sql) {
            if ($wpdb->query($sql) === false) {
                $failed_queries[] = $wpdb->last_query;
                $errors[] = $wpdb->last_error;
                $complete = false;
            } elseif ($wpdb->query($sql) === 0) {
                $successful_queries[] = $wpdb->last_query;
            } else {
                $successful_queries[] = $wpdb->last_query;
            }
        }

        return array('complete' => $complete, 'errors' => $errors, 'failed_queries' => $failed_queries, 'successful_queries' => $successful_queries);
    }
}

if (!function_exists('rokgallery_mootools_remove')) {
    /*
    * removes other instances of mootools
    */
    function rokgallery_mootools_remove()
    {
        global $wp_scripts, $pagenow, $gantry, $wp;
        $page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : '';
        $post_type = (isset($wp->query_vars['post_type'])) ? $wp->query_vars['post_type'] : (isset($_REQUEST['post_type']) ? $_REQUEST['post_type']: '');

        //if rokcommon isn't installed and/or activated we deactivate rokgallery
        if (!rg_rokcommon_check()) return;

        //only strip mootools when needed and only in widget page if gantry is not present
        if ((is_admin() && $pagenow == 'widgets.php' && !$gantry)
            || (is_admin() && $pagenow == 'admin-ajax.php')
            || (is_admin() && $pagenow == 'post.php')
            || (is_admin() && $pagenow == 'post-new.php')
            || (is_admin() && $page == 'rokgallery-options')
            || (is_admin() && $page == 'rokgallery-default')
            || (!is_admin())
        ) {
            foreach ($wp_scripts->registered as $script) {
                switch ($script->handle) {
                    case 'mootools.js':
                        wp_deregister_script($script->handle);
                        break;
                    default:
                        break;
                }
            }
            foreach ($wp_scripts->queue as $script) {
                switch ($script->handle) {
                    case 'mootools.js':
                        wp_dequeue_script($script->handle);
                        break;
                    default:
                        break;
                }
            }
        }
    }
}

if (!function_exists('rokgallery_mootools_init')) {
    /**
     * adds rokgallery mootools
     */
    function rokgallery_mootools_init()
    {
        if (defined('ROKGALLERY_ERROR_MISSING_LIBS') && ROKGALLERY_ERROR_MISSING_LIBS==true) return;

        global $pagenow, $gantry, $wp;
        $page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : '';
        $post_type = (isset($wp->query_vars['post_type'])) ? $wp->query_vars['post_type'] : (isset($_REQUEST['post_type']) ? $_REQUEST['post_type']: '');

        //if rokcommon isn't installed and/or activated we deactivate rokgallery
        if (!rg_rokcommon_check()) return;

        //only load mootools when needed and only in widget page if gantry is not present
        if (!is_admin() || ((is_admin() && $pagenow == 'widgets.php' && !$gantry) ||
            (is_admin() && $pagenow == 'admin-ajax.php') ||
            (is_admin() && $pagenow == 'post.php') ||
            (is_admin() && $pagenow == 'post-new.php') ||
            (is_admin() && $page == 'rokgallery-options') ||
            (is_admin() && $page == 'rokgallery-default'))
        ) {
            RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.js')->getURL('mootools.js'));
        }
    }
}

add_action('admin_init', array('RokGallery_Plugin', 'init'));
add_action('admin_menu', array('RokGallery_Plugin', 'admin_menu'));

add_action('admin_init', 'rokgallery_mootools_remove', 9999);
add_action('admin_init', 'rokgallery_mootools_init', -50);
add_action('init', 'rokgallery_mootools_remove', 9999);
add_action('init', 'rokgallery_mootools_init', -50);

register_activation_hook(ROKGALLERY_PLUGIN_FILE, 'rokgallery_activate');
register_uninstall_hook(ROKGALLERY_PLUGIN_FILE, 'rokgallery_uninstall');