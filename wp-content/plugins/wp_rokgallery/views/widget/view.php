<?php
/**
 * @version   $Id: view.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


class RokGallery_Views_Widget_View
{

    /**
   	 * @var object
   	 */
    var $view;

    /**
     * @param null $instance
     * @return \stdClass
     */
    public function getView($instance = null)
    {
        $options = get_option('rokgallery_plugin_settings');        
        
        $gallery = RokGallery_Model_GalleryTable::getSingle($instance['gallery_id']);
        if ($gallery == false)
        {
            rc_e('No RokGallery has ben Selected');
            return;
        }


        $view = new stdClass();
        foreach ($instance as $key => $val) {
            $view->$key = $val;
        }
        $view->image_width = str_replace('px', '', $gallery->width);
        $view->image_height = str_replace('px', '', $gallery->height);
        $view->thumb_width = str_replace('px', '', $gallery->thumb_xsize);
        $view->thumb_height = str_replace('px', '', $gallery->thumb_ysize);
        $view->autoplay_delay = $instance['autoplay_delay'] * 1000;
        $view->showcase_autoplay_delay = $instance['showcase_autoplay_delay'] * 1000;
        $view->context = 'rokgallery.widget.' . $view->layout;
        $view->moduleid = $instance['widget_id'];

        $widget_slices = new RokGallery_Widgets_Slices();
        $view->slices = $widget_slices->getSlices($instance);

        return $view;
    }
}