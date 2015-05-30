<?php
/**
 * @version   $Id: view.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Views_GalleryManager_View
{

    function __construct(){

    }

    /**
     * @param null $instance
     * @return \stdClass
     */
    public function getView($instance = null)
    {

        $id = isset($instance['id']) ? (int)$instance['id'] : (int)0;
        $name = isset($instance['name']) ? $instance['name'] : null;
        $force_fixed_size = isset($instance['fixed']) ? $instance['fixed'] : null;
        $field_id = isset($instance['field_id']) ? $instance['field_id'] : null;

        $view = new stdClass();
        $view->rtmodel = new RokGallery_Site_DetailModel($id, null, null, null);

        $galleries = RokGallery_Model_GalleryTable::getAll();
        $current_gallery = false;
        if ($id != null) {
            $current_gallery = RokGallery_Model_GalleryTable::getSingle($id);
        }

        if ($name != null) {
            $view->default_name = $name;// . rc__('ROKGALLERY_GALLERY_CREATE_DEFAULT_EXTENSION');
        }

        $nonce = wp_create_nonce('rokgallery-ajax-nonce');
        $view->base_ajax_url = get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action=rokgallery&nonce='.$nonce;

        $view->current_gallery_id = $id;
        $view->force_fixed_size = $force_fixed_size;
        $view->field_id = $field_id;
        $view->galleries = $galleries;
        $view->current_gallery = $current_gallery;
        $view->base_context = 'rokgallery';
        $view->context = 'rokgallery.gallerymanager';

        return $view;

    }
}
