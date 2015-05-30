<?php
/**
 * @version   $Id: functions.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * @return bool
 */
function rg_db_check()
{
    global $wpdb;
    if (!$wpdb->query("SHOW tables LIKE '%rokgallery_files%';")) {
        update_option('rokgallery_db_installed',false);
        rg_force_deactivate();
        return false;
    } else {
        update_option('rokgallery_db_installed',false);
        return true;
    }
}

/**
 * @param $className
 *
 * @return mixed
 * @throws Exception
 */
function rg_load_class($className)
{
    if (class_exists($className)) return;

    try {
        $parts = explode('_', strtolower($className));
        array_shift($parts); //remove RokGallery
        $parts = array_reverse($parts); //reverse to get file name
        $fileName = array_shift($parts) .  ".php";
        $parts = array_reverse($parts); //reverse back to get path
        $filePath = implode($parts, DS); //rebuild path

        $full_file_path = @realpath(realpath(dirname(__FILE__) . DS . $filePath . DS . $fileName));
        if (file_exists($full_file_path) && is_readable($full_file_path) && is_file($full_file_path)) require_once($full_file_path);
    } catch (Exception $e) {
        throw $e;
    }
}
spl_autoload_register('rg_load_class');
/**
 * @param        $name
 * @param null   $value
 * @param string $namespace
 *
 * @return null
 */
function rg_set_session_var($name, $value = null, $namespace = 'default')
{
    $old = isset($_SESSION[$namespace][$name]) ? $_SESSION[$namespace][$name] : null;

    if (null === $value) {
        unset($_SESSION[$namespace][$name]);
    } else {
        $_SESSION[$namespace][$name] = $value;
    }

    return $old;
}

/**
 * @param        $name
 * @param null   $default
 * @param string $namespace
 *
 * @return null
 */
function rg_get_session_var($name, $default = null, $namespace = 'default')
{

    if (isset($_SESSION[$namespace][$name])) {
        return $_SESSION[$namespace][$name];
    }
    return $default;
}

/**
 * @param $var
 *
 * @return mixed
 */
function rg_escape($var)
{
    if (in_array('htmlspecialchars', array('htmlspecialchars', 'htmlentities'))) {
        return call_user_func('htmlspecialchars', $var, ENT_COMPAT, 'UTF-8');
    }

    return call_user_func('htmlspecialchars', $var);
}

/**
 * @param $str
 *
 * @return string
 */
function rg_smartstripslashes($str)
{
    $cd1 = substr_count($str, "\"");
    $cd2 = substr_count($str, "\\\"");
    $cs1 = substr_count($str, "'");
    $cs2 = substr_count($str, "\\'");
    $tmp = strtr($str, array(
        "\\\"" => "",
        "\\'" => ""
    ));
    $cb1 = substr_count($tmp, "\\");
    $cb2 = substr_count($tmp, "\\\\");
    if ($cd1 == $cd2 && $cs1 == $cs2 && $cb1 == 2 * $cb2) {
        return strtr($str, array(
            "\\\"" => "\"",
            "\\'" => "'",
            "\\\\" => "\\"
        ));
    }
    return $str;
}

/**
 * @param $instance
 *
 * @return array
 */
function rg_parse_custom_post($instance)
{
    $new_instance = array();
    foreach ($instance as $key => $value) {
        if (is_array($value) && count($value) == 1) {
            $new_instance[$key] = $value['0'];
        } else {
            $new_instance[$key] = $value;
        }

    }
    return $new_instance;
}

/**
 * @param $instance
 * @param $defaults
 *
 * @return array
 */
function rg_parse_options($instance, $defaults)
{
    $instance = wp_parse_args((array)$instance, $defaults);
    foreach ($instance as $variable => $value) {
        //we borrow this from the widget class
        $$variable = rg_cleanOutputVariable($variable, $value);
        $instance[$variable] = $$variable;
    }
    return $instance;
}

/**
 * @param $variable
 * @param $value
 *
 * @return array|string
 */
function rg_cleanOutputVariable($variable, $value)
{
    if (is_string($value)) {
        return htmlspecialchars($value);
    } elseif (is_array($value)) {
        foreach ($value as $subvariable => $subvalue) {
            $value[$subvariable] = rg_cleanOutputVariable($subvariable, $subvalue);
        }
        return $value;
    }
    return $value;
}

/**
 * @param $variable
 * @param $value
 *
 * @return array|string
 */
function rg_cleanInputVariable($variable, $value)
{
    if (is_string($value)) {
        return stripslashes($value);
    } elseif (is_array($value)) {
        foreach ($value as $subvariable => $subvalue) {
            $value[$subvariable] = rg_cleanInputVariable($subvariable, $subvalue);
        }
        return $value;
    }
    return $value;
}

/**
 * @param $field
 *
 * @return string
 */
function rg_get_option_id($field)
{
    return $field;
}

/**
 * @param $field
 *
 * @return string
 */
function rg_get_option_name($field)
{
    return 'rokgallery_plugin_settings' . '[' . $field . ']';
}

/**
 * @param $field_name
 * @return string
 */
function rg_get_field_id($instance, $field_name)
{
    return 'widget-' . $instance['id_base'] . '-' . $instance['number'] . '-' . $field_name;
}


/**
 * @param $field_name
 * @return string
 */

function rg_get_field_name($instance, $field_name)
{
    return 'widget-' . $instance['id_base'] . '[' . $instance['number'] . '][' . $field_name . ']';
}


/**
 * @param $disabled
 * @param $current
 *
 * @return string
 */
function rg_disabled($disabled, $current, $echo = true)
{
    if (is_array($disabled)) {
        $html = (in_array($current, $disabled)) ? ' disabled="disabled"' : '';
    } else {
        $html = ($current == $disabled) ? ' disabled="disabled"' : '';
    }
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * @param $selected
 * @param $current
 *
 * @return string
 */
function rg_selected($selected, $current, $echo = true)
{
    if (is_array($selected)) {
        $html = (in_array($current, $selected)) ? ' selected="selected"' : '';

    } else if(is_int($selected) || is_int($current)) {
        $html = ((int)$current == (int)$selected) ? ' selected="selected"' : '';
    } else {
        $html = ($current == $selected) ? ' selected="selected"' : '';
    }
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * @param $checked
 * @param $current
 *
 * @return string
 */
function rg_checked($checked, $current, $echo = true)
{
    if (is_array($checked)) {
        $html = (in_array($current, $checked)) ? ' checked="checked"' : '';
    } else {
        $html = ($current == $checked) ? ' checked="checked"' : '';
    }
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * Call galleryManager window content via admin-ajax
 * there is no iframe handler in WP so we use ajax
 * @return mixed
 */
function rokgallery_gallerymanager_ajax()
{
    global $wpdb;

    $nonce = RokGallery_Request::getString('nonce');
    if (!wp_verify_nonce($nonce, 'rokgallery-ajax-nonce')) {
        return;
    }

    //setup instance
    $instance['id'] = RokGallery_Request::getInt('id', null, 'get');
    $instance['name'] = RokGallery_Request::getString('name', null, 'get');
    $instance['fixed'] = RokGallery_Request::getString('fixed', null, 'get');
    $instance['field_id'] = RokGallery_Request::getString('field_id', null, 'get');

    //get view
    $view = new RokGallery_Views_GalleryManager_View();
    $view = $view->getView($instance);

    //render popup content
    ob_start();
    if ($view->inline_js) {
        RokCommon_Header::addInlineScript($view->inline_js);
    }
    if ($view->inline_css) {
        RokCommon_Header::addInlineStyle($view->inline_css);
    }
    //render popup content
    //there is no iframe handler in WP so we render the the popup content and echo it on the window page,
    //our other option is to use ajax but then we don't have the full framework
    RokCommon_Header::addInlineScript(RokCommon_Composite::get($view->context)->load('javascript.php', array('that' => $view)));
    echo RokCommon_Composite::get('rg_views.gallerymanager')->load('default.php', array('view' => $view));
    $content = ob_get_contents();
    ob_end_clean();

    echo RokCommon_Composite::get('rg_views.gallerymanager')->load('window.php', array('content' => $content));
    // IMPORTANT: don't forget to "exit"
    exit;
}


/**
 * Call galleryPicker window content via admin-ajax
 * there is no iframe handler in WP so we use ajax
 * @return mixed
 */
function rokgallery_gallerypicker_ajax()
{
    global $wpdb;

    //    $nonce = RokGallery_Request::getString('nonce');
    //    if (!wp_verify_nonce($nonce, 'rokgallery-ajax-nonce')) {
    //        return;
    //    }

    $instance = new stdClass();
    $instance->action = RokGallery_Request::getstring('model_action', 'rokgallery_gallerypicker', 'get');
    $instance->textarea = RokGallery_Request::getString('textarea', 'content', 'get');
    $instance->inputfield = RokGallery_Request::getString('inputfield', 'content', 'get');
    $instance->show_menuitems = RokGallery_Request::getBool('show_menuitems', 1, 'post');
    $instance->gallery_id = RokGallery_Request::getInt('gallery_id', 0, 'post');
    $instance->file_id = RokGallery_Request::getInt('file_id', 0, 'post');
    $instance->page = RokGallery_Request::getInt('page', 1, 'post');

    //get view
    $view = new RokGallery_Views_GalleryPicker_View();
    $view = $view->getView($instance);

    //RokCommon_Header::addScript(RokCommon_Composite::get('rokgallery.gallerypicker')->loadAll('includes.php', array('that'=>$view)));

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

    echo RokCommon_Composite::get('rg_views.gallerypicker')->load('window.php', array('content' => $content));
    // IMPORTANT: don't forget to "exit"
    exit;
}


/**
 * Call galleryPicker window content via admin-ajax
 * there is no iframe handler in WP so we use ajax
 * @return mixed
 */
function rokgallery_postlist_ajax()
{
    $nonce = RokGallery_Request::getString('nonce');
    if (!wp_verify_nonce($nonce, 'rokgallery-ajax-nonce')) {
        return;
    }

    $instance = new stdClass();
    $instance->post_type = RokGallery_Request::getString('post_type', '');
    $instance->category = RokGallery_Request::getString('category', '');
    $instance->search = RokGallery_Request::getString('search', '');
    $instance->page = RokGallery_Request::getInt('page', 1);
    $instance->orderby = RokGallery_Request::getString('orderby', 'title');
    $instance->order = RokGallery_Request::getString('order', 'ASC');

    //get view
    $view = new RokGallery_Views_Default_View();
    $view = $view->getPostList($instance);

    //add thickbox
    wp_enqueue_script('jquery');
    wp_enqueue_style('thickbox');
    add_thickbox();
    //add adminstuff
    wp_enqueue_style('wp-admin');
    wp_enqueue_style('colors');

    RokCommon_Header::addStyle(ROKGALLERY_PLUGIN_URL . '/rokgallery.css');

    //render popup content
    ob_start();
    if ($view->inline_js) {
        RokCommon_Header::addInlineScript($view->inline_js);
    }
    if ($view->inline_css) {
        RokCommon_Header::addInlineStyle($view->inline_css);
    }
    echo RokCommon_Composite::get('rg_views.default')->load('postlist.php', array('view' => $view));
    $content = ob_get_contents();
    ob_end_clean();

    echo RokCommon_Composite::get('rg_views.default')->load('window.php', array('content' => $content));
    // IMPORTANT: don't forget to "exit"
    exit;
}


/**
 * @return mixed
 * default rokgallery ajax routing function
 */
function rokgallery_ajax()
{
    global $wpdb;

    $nonce = RokGallery_Request::getString('nonce');
    if (!wp_verify_nonce($nonce, 'rokgallery-ajax-nonce')) {
        return;
    }

    try {
        $model = RokGallery_Request::getString('model');
        $action = RokGallery_Request::getString('model_action');
        if ($model == 'loves') { //is_admin() does not work
            RokCommon_Ajax::addModelPath(ROKGALLERY_PLUGIN_PATH . '/lib/RokGallery/Site/Ajax/Model', 'RokGallerySiteAjaxModel');
        } else {
            RokCommon_Ajax::addModelPath(ROKGALLERY_PLUGIN_PATH . '/lib/RokGallery/Admin/Ajax/Model', 'RokGalleryAdminAjaxModel');
        }

        if (isset($_REQUEST['params'])) {
            $params = rg_smartstripslashes($_REQUEST['params']);
        }
        echo RokCommon_Ajax::run($model, $action, $params);
        // IMPORTANT: don't forget to "exit"
        exit;

    } catch (Exception $e) {
        $result = new RokCommon_Ajax_Result();
        $result->setAsError();
        $result->setMessage($e->getMessage());
        echo json_encode($result);
        // IMPORTANT: don't forget to "exit"
        exit;

    }
}

/**
 * @static
 * @return bool
 */
function rg_rokgallery_check()
{
	//check for rokgallery
    $rokgallery = (file_exists(ROKGALLERY_PLUGIN_PATH . '/rokgallery.php')) ? true : false;
    $activated = false;

	//check if rokgallery is activated
	$options   = get_option('active_plugins');
	foreach ($options as $k => $v) {
		if (strpos($v, 'rokgallery') !== false) {
			$activated = true;
		}
	}
    //multi-site
    if(is_multisite()){
        $wpmu_options   = get_site_option( 'active_sitewide_plugins');
        foreach ($wpmu_options as $k => $v) {
            if (strpos($k, 'rokgallery') !== false) {
                $activated = true;
            }
        }
    }
    if (($rokgallery) && ($activated)) {
        return true;
    } else if (($rokgallery) && (!$activated)) {
        return true;
    } else {
        return false;
    }
}

/**
 * @static
 * @return bool
 */
function rg_rokcommon_check()
{
	//check for rokcommon
	$rokcommon = (file_exists(ROKCOMMON_LIB_PATH . '/rokcommon.php')) ? true : false;
    $activated = false;

	//check if rokcommon is activated
	$options   = get_option('active_plugins');
	foreach ($options as $k => $v) {
		if (strpos($v, 'rokcommon') !== false) {
			$activated = true;
		}
	}
    //multi-site
    if(is_multisite()){
        $wpmu_options   = get_site_option( 'active_sitewide_plugins');
        foreach ($wpmu_options as $k => $v) {
            if (strpos($k, 'rokcommon') !== false) {
                $activated = true;
            }
        }
    }
    if (($rokcommon) && ($activated)) {
        return true;
    } elseif (($rokcommon) && (!$activated)) {
        rg_force_deactivate();
        rokgallery_set_admin_message('error',__('RokGallery Plugin requires the Rokcommon Library Plugin to be <strong>Installed</strong> and <strong>Activated</strong> before you can activate or use RokGallery.'));
        return false;
    } else {
        rg_force_deactivate();
        rokgallery_set_admin_message('error',__('RokGallery Plugin requires the Rokcommon Library Plugin to be <strong>Activated</strong> before you can activate or use RokGallery.'));
        return false;
    }
}

/**
 * @static
 * removes rokgallery from active_plugins options in db
 */
function rg_force_deactivate()
{
    $options = array();
    if(get_option('active_plugins'))
        $options = get_option('active_plugins');
    foreach ($options as $k => $v) {
        if (strpos($options[$k], 'rokgallery') !== false) {
            unset($options[$k]);
        }
    }
    update_option('active_plugins', $options);
    rokgallery_set_admin_message('error',__("RokGallery Plugin has been deactivated."));
}

function rokgallery_set_admin_message($type, $message, $timeout = 30)
{
   if (empty($message)) return;
   $tansitent_id = md5('rokgallery-message-' . $_COOKIE['PHPSESSID']);
   $exisisting          = get_transient($tansitent_id);
   if ($exisisting == false){
       $exisisting = array();
   }
   $msgobject= new stdClass();
   $msgobject->type = $type;
   $msgobject->message = $message;
   $chksum = md5($type.$message);
   $exisisting[$chksum] = $msgobject;
   set_transient($tansitent_id, $exisisting, $timeout);
}

function rokgallery_get_admin_messages()
{
   $ret          = null;
   $tansitent_id = md5('rokgallery-message-' . $_COOKIE['PHPSESSID']);
   $ret          = get_transient($tansitent_id);
   if ($ret != false) delete_transient($tansitent_id);
   return $ret;
}

function rokgallery_display_admin_notices()
{
    $buffer = '';
    $messages = rokgallery_get_admin_messages();
    if($messages){
        foreach ($messages as $message){
            $buffer .= '<div id="message" class="'.$message->type.'">'."\n";
            $buffer .= "<p>" . $message->message . "</p>\n";
            $buffer .= "</div>";
        }
    }
    echo $buffer;
}

add_action("admin_notices", 'rokgallery_display_admin_notices', 100);

add_action('wp_ajax_rokgallery', 'rokgallery_ajax');
add_action('wp_ajax_nopriv_rokgllery', 'rokgallery_ajax');

add_action('wp_ajax_rokgallery_gallerymanager', 'rokgallery_gallerymanager_ajax');
add_action('wp_ajax_nopriv_rokgallery_gallerymanager', 'rokgallery_gallerymanager_ajax');

add_action('wp_ajax_rokgallery_gallerypicker', 'rokgallery_gallerypicker_ajax');
add_action('wp_ajax_nopriv_rokgallery_gallerypicker', 'rokgallery_gallerypicker_ajax');

add_action('wp_ajax_rokgallery_postlist', 'rokgallery_postlist_ajax');
add_action('wp_ajax_nopriv_rokgallery_postlist', 'rokgallery_postlist_ajax');