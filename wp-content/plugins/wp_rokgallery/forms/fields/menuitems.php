<?php
/**
 * @version   $Id: menuitems.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Forms_Fields_MenuItems
{
    /**
     * @param $fieldName
     * @param $fieldId
     * @param $fieldValue
     * @param string $class
     * @return string
     */
    public static function getInput($fieldName, $fieldId, $fieldValue, $class = 'widefat')
    {
        $args = array(
            'order' => 'ASC',
            'orderby' => 'menu_order',
            'post_type' => 'nav_menu_item',
            'post_status' => 'publish',
            'output' => ARRAY_A,
            'output_key' => 'menu_order',
            'nopaging' => true,
            'update_post_term_cache' => false
        );

        $html = '<select class="' . $class . '" id="' . $fieldId . '" name="' . $fieldName . '">';
        $menus = wp_get_nav_menus();

        foreach ($menus as $menu) {

            $html .= '<option value="">' . '- ' . $menu->name . ' Menu -' . '</option>';
            $menu_items = wp_get_nav_menu_items($menu->term_id, $args);

            foreach ($menu_items as $key => $menu_item) {
                $html .= '<option value="' . $menu_item->url . '"' . (($fieldValue == $menu_item->url) ? ' selected="selected"' : '') . '>&#x221F; ' . $menu_item->title . '</option>';
            }
        }
        $html .= '</select>';

        return $html;
    }

}