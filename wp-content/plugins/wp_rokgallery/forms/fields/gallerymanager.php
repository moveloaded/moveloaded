<?php
 /**
 * @version   $Id: gallerymanager.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Forms_Fields_GalleryManager
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
     * @param $titleId
     * @return string
     */
    public static function getInput($instance = null, $fieldName, $fieldId, $fieldValue, $titleId)
    {
        $gallery_name = '';
        if (!empty($fieldValue)) {
            $gallery = RokGallery_Model_GalleryTable::getSingle((int)$fieldValue);
            if ($gallery === false) {
                $fieldValue = null;
            }
            else {
                $gallery_name = $gallery->name;
            }
        }

        //add thickbox
        //TODO have to use stndard enque here until rokcommon header is improved
        wp_enqueue_script('jquery');
        wp_enqueue_style('thickbox');
        add_thickbox();

        $cleanField = str_replace('-', '_', $fieldId);
        $js = "
        if (typeof RokGalleryPopup == 'undefined') var RokGalleryPopup = {};
        if (typeof RokGalleryFixed == 'undefined') var RokGalleryFixed = {};
		RokGalleryPopup['".$cleanField."'] = RokGalleryID".$cleanField." = null;
        RokGalleryFixed['".$cleanField."'] = 0;
        window.addEvent('domready', function(){
            var mooVersion = MooTools.version;
            if (RokGalleryPopup['".$cleanField."']) return;

            RokGalleryPopup['".$cleanField."'] = document.id('gallery-popup-".$cleanField."');
            RokGalleryID".$cleanField." = document.id('".$fieldId ."_id');
            var titles = ['#".$titleId."', '#title-prompt-text'],
                title = '',
                href = RokGalleryPopup['".$cleanField."'].get('href');

            titles.each(function(selector){
                var element = document.getElement(selector); if (element) title = element;
            });

            RokGalleryPopup['".$cleanField."'].addEvent('mouseover', function(){
                RokGalleryUpdateTitle".$cleanField."(title, href);
            });
        });

        function RokGalleryUpdateTitle".$cleanField."(title, href){
            title = title ? (title.get('value') ? title.get('value') : 'RokGallery'): 'RokGallery';
            var args = '&id=' + RokGalleryID".$cleanField.".get('value') + '&name=' + title + '&fixed=' + RokGalleryFixed['".$cleanField."'] + '&field_id=".$fieldId."&nocache=' + (Date.now());
            var iframe = '&TB_iframe=true&height=555&width=425&modal=false';
            RokGalleryPopup['".$cleanField."'].set('href', href + args + iframe);
        }";

        $nonce = wp_create_nonce('rokgallery-ajax-nonce');

        $link = get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action=rokgallery_gallerymanager&nonce='.$nonce;

        RokCommon_Header::addInlineScript($js);

        $html = "\n" . '<div style="float: left;"><input style="background: #ffffff;" class="rg_gallery_id_name" type="text" id="' . $fieldId . '_name" value="' . htmlspecialchars($gallery_name, ENT_QUOTES, 'UTF-8') . '" disabled="disabled" /></div>';
        $html .= "\n" .'<div style="float: left; margin: 4px 0 0 5px;" class="button2-left"><div class="blank"><a id="gallery-popup-'.$cleanField.'" class="thickbox" title="Select a Gallery"  href="'.$link.'">Select</a></div></div>';
        $html .= "\n" . '<input type="hidden" id="' . $fieldId . '_id" class="rg_gallery_id_id" name="' . $fieldName . '" value="' . $fieldValue . '" />';
        $html .= "\n" . '<div style="clear:both;"></div>';

        return $html;
    }
}
