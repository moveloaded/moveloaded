<?php
 /**
 * @version   $Id: include.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

if (!defined('WORDPRESS_ROKGALLERY_LIB')) {
    /**
     * load rokcommon and rokgallery library
     */
    define('WORDPRESS_ROKGALLERY_LIB', 'WORDPRESS_ROKGALLERY_LIB');
    if (!defined('ROKCOMMON_LIB_PATH')){
        define('ROKCOMMON_LIB_PATH', ROKCOMMON_LIB_PATH);
    }

    $include_file = @realpath(realpath(ROKGALLERY_PLUGIN_PATH . '/lib/include.php'));
    $included_files = get_included_files();
    if (!in_array($include_file, $included_files) && ($loaderrors = require_once($include_file)) !== 'ROKGALLERY_LIB_INCLUDED') {
        return $loaderrors;
    }
    rg_db_check();
    //if rokcommon isn't installed and/or activated we deactivate rokgallery if rokgallery isn't installed w/ its db we don't allow rokcommon to be called
    if (file_exists(ROKCOMMON_LIB_PATH) && rg_rokcommon_check() && rg_db_check()) {
        RokGallery_Doctrine::addModelPath(ROKGALLERY_PLUGIN_PATH . '/lib');
        RokGallery_Doctrine::useMemDBCache('RokGallery');

        RokCommon_Composite::addPackagePath('rokgallery', ROKGALLERY_PLUGIN_PATH . '/templates');
        $container = RokCommon_Service::getContainer();
        RokCommon_Composite::addPackagePath('rokgallery', $container->platforminfo->getDefaultTemplatePath() . '/overrides/rokgallery/templates',20);

        RokCommon_Composite::addPackagePath('rg_views', ROKGALLERY_PLUGIN_PATH . '/views');
        RokCommon_Composite::addPackagePath('rg_assets', ROKGALLERY_PLUGIN_PATH . '/assets');
        RokCommon_Composite::addPackagePath('rg_forms', ROKGALLERY_PLUGIN_PATH . '/forms');
        RokCommon_Composite::addPackagePath('rg_tinymce', ROKGALLERY_PLUGIN_PATH . '/tinymce');
    }
}
return 'WORDPRESS_ROKGALLERY_LIB_INCLUDED';