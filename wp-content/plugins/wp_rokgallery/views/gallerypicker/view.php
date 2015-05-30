<?php
/**
 * @version   $Id: view.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Views_GalleryPicker_View
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

        global $wpdb;

        $view = new stdClass();

        $action = $instance->action;
        $textarea = $instance->textarea;
        $inputfield = $instance->inputfield;
        $show_menuitems = $_GET['show_menuitems'] == '1' ? 1 : 0;//$instance->show_menuitems;
        $gallery_id = $instance->gallery_id;
        $file_id = $instance->file_id;
        $page = $instance->page ? $instance->page : 1;

        $options = get_option('rokgallery_plugin_settings');

        $model = new RokGallery_Admin_MainPage();

        $items_per_page = ($options['items_per_page']) ? $options['items_per_page'] * 2 : 12;

        if (!$gallery_id) $view->galleries = RokGallery_Model_GalleryTable::getAll();
        else $view->galleries = RokGallery_Model_GalleryTable::getSingle($gallery_id);
        if (!$file_id) {
            $view->files = $model->getFiles($page, $items_per_page);
        }
        else {
            $view->filter = json_decode('{"type":"id", "operator":"is", "query":' . (int)$file_id . '}');
            $view->files = $model->getFiles($page, $items_per_page, array($view->filter))->getFirst();
        }

        $pager = $model->getPager($page, $items_per_page);

        $next_page = $page + 1;
        $next_page = ($page == $pager->getLastPage()) ? false : $next_page;

        $nonce = wp_create_nonce('rokgallery-ajax-nonce');
        $url = get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=rokgallery&nonce=' . $nonce;

        $images_url = ROKGALLERY_PLUGIN_URL . '/assets/images/';
        $application = ROKGALLERY_PLUGIN_PATH . '/assets/application/';

        $modal_url = get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action='.$action;
        if ($action != 'rokgallery_tinymce') $modal_url .= '&nonce=' . $nonce;
        if ($textarea !== false) $modal_url .= "&textarea=" . $textarea;
        if ($inputfield !== false) $modal_url .= '&inputfield=' . $inputfield;
        //if ($page !== false) $modal_url .= '&page=' . $page;


        $more_pages = ($next_page == false) ? "false" : "true";

        $view->inline_js = 'var RokGallerySettings = {
            application: "' . $application . '",
            images: "' . $images_url . '",
            next_page: "' . $next_page .'",
            more_pages: ' . $more_pages. ',
            items_per_page: "' . $items_per_page .'",
            total_items: ' . $pager->getNumResults() . ',
            url: "' . $url . '",
            ajaxVars: {
                action: "model_action",
                model: "model"
            },
            modal_url: "'.$modal_url.'",
            textarea: "'.$textarea.'",
            inputfield: "'.$inputfield.'",
            token: "' . $nonce . '",
            session: {
				name: "' . session_name() . '",
				id: "' . session_id() . '"
            },
            order: ["order-created_at", "order-desc"]
        };';

        $args = array(
            'order' => 'ASC',
            'orderby' => 'menu_order',
            'post_type' => 'nav_menu_item',
//            'post_status' => 'publish',
            'output' => ARRAY_A,
            'output_key' => 'menu_order',
            'nopaging' => true,
            'update_post_term_cache' => false
        );

        $menus = wp_get_nav_menus();

        $new_menuitems = array();
        foreach ($menus as $menu) {
            $menuitems = wp_get_nav_menu_items($menu->term_id, $args);
            foreach ($menuitems as $key => $menu_item) {
                if($menu_item->object=='rokgallery'){
                    $new_menuitems[] = $menu_item;
                }
            }
        }
        $view->menuitems = $new_menuitems;

        if ($view->galleries === false) $view->galleries = array();
        if ($view->files === false) $view->files = array();

        $view->base_ajax_url = $url;
        $view->images_path = $images_url;
        $view->gallery_id = $gallery_id;
        $view->file_id = $file_id;
        $view->page = $page;
        $view->total_items_in_filter = $pager->getNumResults();
        $view->items_to_be_rendered = $pager->getResultsInPage();
        $view->currently_shown_items = $pager->getLastIndice();
        $view->totalFilesCount = $pager->getNumResults();
        $view->show_menuitems = $show_menuitems;
        $view->context = 'rokgallery.gallerypicker';

        return $view;

    }
}
