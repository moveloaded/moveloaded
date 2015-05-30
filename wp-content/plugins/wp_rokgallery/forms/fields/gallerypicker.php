<?php
 /**
 * @version   $Id: gallerypicker.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Forms_Fields_GalleryPicker
{

    /**
     * @var
     */
    var $html;

    /**
     * @param null $instance
     * @param $fieldName
     * @param $fieldId
     * @param $fieldValue
     * @return string
     */
    public static function getInput($instance = null, $fieldName, $fieldId, $fieldValue)
    {
        //add thickbox
        //TODO have to use stndard enque here until rokcommon header is improved
        wp_enqueue_script('jquery');
        wp_enqueue_style('thickbox');
        add_thickbox();

        $nonce = wp_create_nonce('rokgallery-ajax-nonce');

        $link = get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action=rokgallery_gallerypicker&nonce='.$nonce.'&TB_iframe=true&height=555&width=425&modal=false';

        $html = "\n" . '<div style="float: left;"><input style="background: #ffffff;" type="text" id="' . $fieldId . '_name" value="' . htmlspecialchars($gallery_name, ENT_QUOTES, 'UTF-8') . '" disabled="disabled" /></div>';
        $html .= '<div class="button2-left"><div class="blank"><a id="gallery-popup" class="thickbox" title="Select a Gallery"  href="' . $link . '">Select</a></div></div>' . "\n";
        $html .= "\n" . '<input type="hidden" id="' . $fieldId . '_id" name="' . $fieldName . '_id" value="' . $fieldValue . '" />';

        return $html;
    }
}
