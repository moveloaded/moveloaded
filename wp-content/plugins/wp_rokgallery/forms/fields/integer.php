<?php
 /**
 * @version   $Id: integer.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Forms_Fields_Integer
{
    /**
     * @param $fieldName
     * @param $fieldId
     * @param $fieldValue
     * @param string $first
     * @param string $last
     * @param string $step
     * @param string $class
     * @return string
     */
    public static function getInput($fieldName, $fieldId, $fieldValue, $first="1", $last="10", $step="1", $class="rokgallery-fields")
    {

        $html = '<select class="'.$class.'" id="'.$fieldId.'" name="'.$fieldName .'">';
        for($i = $first; $i <= $last; ){
            $html .= '<option value="'.$i.'"'.(($fieldValue==$i)?' selected="selected"':'').'>'.$i.'</option>';
            $i=$i+$step;
        }
        $html .= '</select>';

        return $html;
    }

}