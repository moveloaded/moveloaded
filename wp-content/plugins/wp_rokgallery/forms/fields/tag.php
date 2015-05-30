<?php
/**
 * @version   $Id: tag.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
class RokGallery_Forms_Fields_Tag
{

    public static function getInput($instance = null, $fieldName, $fieldId, $fieldValue, $class=false, $multiple=false, $size=false)
    {

        $tags = array();//get_tags();

        $size = ($size) ? ' size="'.$size.'"' : '';
        $class = ($class) ? 'inputbox '.$class : 'inputbox';
        $multiple = ($multiple) ? ' multiple="multiple"' : '';

        if (count($tags)) {
            $html = '<select class="'.$class.'" id="'.$fieldId.'" name="'.$fieldName . $multiple . $size .'">';

            foreach ($tags as $tag)
            {
                $html .= '<option value="'.$tag->id.'"'.(($fieldValue==$tag->id)?' selected="selected"':'').'>'.$tag->name.'</option>';
            }

            $html .= '</select>';

        }
        else {
            $html = rc__('NO_TAGS_MESSAGE');
        }
    return $html;
    }
}
