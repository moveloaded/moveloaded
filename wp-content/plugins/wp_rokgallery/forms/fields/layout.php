<?php
 /**
 * @version   $Id: layout.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Forms_Fields_Layout
{
    /**
     * @param null $instance
     * @param $fieldName
     * @param $fieldId
     * @param $fieldValue
     * @return string
     */
    public static function getInput($instance = null, $fieldName, $fieldId, $fieldValue)
    {
        $cleanField = str_replace('-', '_', preg_replace("/layout$/", "gallery_id", $fieldId));
        $js =
        "((function(){
            window.addEvent('domready', function(){
                RokGalleryLayoutChanger.delay(500, RokGalleryLayoutChanger, ['".$fieldId."', '".$instance['layout']."']);
                RokGalleryPopup['".$cleanField."'].fireEvent('mouseover');
            });
        })());";

        RokCommon_Header::addInlineScript($js);

        $html =
        '<select class="widefat" id="' .$fieldId. '" name="' .$fieldName. '">
            <option rel="false" value="grid"'. self::is_selected($instance['layout'], 'grid').'>'. rc__('ROKGALLERY_GRID').'</option>
            <option rel="true" value="showcase"'. self::is_selected($instance['layout'], 'showcase').'>'. rc__('ROKGALLERY_SHOWCASE').'</option>
            <option rel="true" value="slideshow"'. self::is_selected($instance['layout'], 'slideshow').'>'. rc__('ROKGALLERY_SLIDESHOW').'</option>
            <option rel="true" value="showcase_responsive"'. self::is_selected($instance['layout'], 'showcase_responsive').'>'. rc__('ROKGALLERY_SHOWCASE_RESPONSIVE').'</option>

        </select>';

        return $html;
    }

    /**
     * @param $selected
     * @param $field
     * @return string
     */
    function is_selected($selected, $field)
    {
        if($selected==$field){
           return ' selected="selected" ';
        }
    }

}
