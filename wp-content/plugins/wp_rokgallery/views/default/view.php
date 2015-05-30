<?php
/**
 * @version   $Id: view.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Views_Default_View
{

    /**
     * @var object
     */
    var $view;

    /**
     * @static
     * @param null $instance
     * @return \stdClass
     */
    public static function getView($instance = null)
    {
        $view = new stdClass();

        $options = get_option('rokgallery_plugin_settings');

        $model = new RokGallery_Admin_MainPage();
        $current_page = 1;
        $items_per_page = ($options['items_per_page']) ? $options['items_per_page'] : 6;
        $items_per_row = ($options['items_per_row']) ? $options['items_per_row'] : 3;

        $files = $model->getFiles($current_page, $items_per_page * 2);
        $pager = $model->getPager($current_page, $items_per_page * 2);


        $next_page = ($current_page == 1) ? 3 : $current_page + 1;
        $next_page = ($current_page == $pager->getLastPage()) ? false : $next_page;

        $more_pages = ($next_page == false) ? "false" : "true";

        $application = ROKGALLERY_PLUGIN_URL . '/assets/application/';
        $images = ROKGALLERY_PLUGIN_URL . '/assets/images/';


        $nonce = wp_create_nonce('rokgallery-ajax-nonce');
        //$path = htmlentities(ABSPATH);
        //$url = ROKGALLERY_PLUGIN_URL . '/ajax.php?nonce=' . $nonce . '&path=' . $path;

        $url = get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=rokgallery&nonce=' . $nonce;


        $view->inline_js = 'var RokGallerySettings = {
			application: "' . $application . '",
			images: "' . $images . '",
			next_page: "' . $next_page . '",
            last_page: "' . $pager->getLastPage() . '",
			more_pages: ' . $more_pages . ',
			items_per_page: "' . $items_per_page . '",
            total_items: ' . $pager->getNumResults() . ',
			url: "' . $url . '",
            ajaxVars: {
                action: "model_action",
                model: "model"
            },
			token: "' . $nonce . '",
			session: {
				name: "' . session_name() . '",
				id: "' . session_id() . '"
			},
			order: ["order-created_at", "order-desc"]
		};';


        $galleries = RokGallery_Model_GalleryTable::getAll();
        if ($galleries === false) {
            $galleries = array();
        }

        $view->files = $files;
        $view->galleries = $galleries;
        $view->total_items_in_filter = $pager->getNumResults();
        $view->items_to_be_rendered = $pager->getResultsInPage();
        $view->next_page = $next_page;
        $view->items_per_page = $items_per_page;
        $view->items_per_row = $items_per_row;
        $view->currently_shown_items = $pager->getLastIndice();
        $view->totalFilesCount = $pager->getNumResults();
        $view->context = 'rokgallery.default';
        $view->toolbar = self::getToolbar();

        if ($view->galleries === false) $view->galleries = array();
        if ($view->files === false) $view->files = array();

        return $view;
    }

    /**
     * @return array
     */
    private function getToolbar()
    {

        $buttons = array();
        $buttons[] = array(
            'name' => 'publish',
            'title' => 'Publish',
            'image' => 'icon-32-publish.png',
            'class' => 'icon-32-publish',
            'id' => 'toolbar-publish',
            'link' => '#',
            'javascript' => ''
        );
        $buttons[] = array(
            'name' => 'unpublish',
            'title' => 'Unpublish',
            'image' => 'icon-32-unpublish.png',
            'class' => 'icon-32-unpublish',
            'id' => 'toolbar-unpublish',
            'link' => '#',
            'javascript' => ''
        );
        $buttons[] = array(
            'name' => 'tag',
            'title' => 'Tag',
            'image' => 'icon-32-tag.png',
            'class' => 'icon-32-tag',
            'id' => 'toolbar-tag',
            'link' => '#',
            'javascript' => ''
        );

        $buttons[] = array(
            'name' => 'delete',
            'title' => 'Delete',
            'image' => 'icon-32-delete.png',
            'class' => 'icon-32-delete',
            'id' => 'toolbar-delete',
            'link' => '#',
            'javascript' => ''
        );

        $buttons[] = array(
            'name' => 'separator',
            'title' => '',
            'image' => '',
            'class' => 'toolbar-separator',
            'id' => 'toolbar-separator',
            'link' => '',
            'javascript' => '',
            'extra_class' => 'separator'
        );
        $buttons[] = array(
            'name' => 'jobs',
            'title' => 'Jobs',
            'image' => 'icon-32-jobs.png',
            'class' => 'icon-32-jobs',
            'id' => 'toolbar-jobs',
            'link' => '#',
            'javascript' => ''
        );
        $buttons[] = array(
            'name' => 'galleries',
            'title' => 'Galleries',
            'image' => 'icon-32-galleries.png',
            'class' => 'icon-32-galleries',
            'id' => 'toolbar-galleries',
            'link' => '#',
            'javascript' => ''
        );
        $buttons[] = array(
            'name' => 'settings',
            'title' => 'Settings',
            'image' => 'icon-32-settings.png',
            'class' => 'icon-32-settings',
            'id' => 'toolbar-settings',
            'link' => 'admin.php?page=rokgallery-options',
            'javascript' => ''
        );
        $buttons[] = array(
            'name' => 'upload',
            'title' => 'Upload',
            'image' => 'cloud-upload.png',
            'class' => 'icon-32-upload',
            'id' => 'toolbar-upload',
            'link' => '#',
            'javascript' => '',
            'extra_class' => 'upload'
        );

        return $buttons;

    }

    /**
     * @static
     * @param null $instance
     * @return stdClass
     */
    public static function getPostList($instance = null)
    {
        $view = new stdClass();

        foreach ($instance as $key => $val) {
            $view->$key = $val;
        }

        $view->post_types = array('post', 'page', 'rokgallery');
        $view->post_statuses = array('publish', 'unpublish');
        $view->images_path = ROKGALLERY_PLUGIN_URL . '/assets/images/';

        $view->nonce = wp_create_nonce('rokgallery-ajax-nonce');
        $view->iframe_link = '&TB_iframe=true&height=555&width=425&modal=true';
        $view->base_link = get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=rokgallery_postlist&nonce=' . $view->nonce;

        $search_args = array();
        if ($view->category)
            $search_args['category'] = $view->category;
        if ($view->orderby)
            $search_args['orderby'] = $view->orderby;
        if ($view->order)
            $search_args['order'] = $view->order;
        if ($view->post_type)
            $search_args['post_type'] = $view->post_type;
        if ($view->post_status)
            $search_args['post_status'] = $view->post_status;

        //build the base_url
        $view->search_link = RokCommon_URL::updateParams($view->base_link, $search_args);

        $link_args = array();
        if ($view->search)
            $link_args['name'] = '%' . $view->search . '%';
        if ($view->category)
            $link_args['category'] = $view->category;
        if ($view->orderby)
            $link_args['orderby'] = $view->orderby;
        if ($view->order)
            $link_args['order'] = $view->order;
        if ($view->post_type)
            $link_args['post_type'] = $view->post_type;
        if ($view->post_status)
            $link_args['post_status'] = $view->post_status;

        //build the base_url
        $view->link = RokCommon_URL::updateParams($view->base_link, $link_args);

        //add additional args to get posts for a single page
        $post_args = array();
        if ($view->search)
            $post_args['name'] = '%' . $view->search . '%';
        if ($view->category)
            $post_args['category'] = $view->category;
        if ($view->orderby)
            $post_args['orderby'] = $view->orderby;
        if ($view->order)
            $post_args['order'] = $view->order;
        if ($view->post_type) {
            $post_args['post_type'] = $view->post_type;
        } else {
            $post_args['post_type'] = $view->post_types;
        }
        $post_args['numberposts'] = 20;
        $post_args['offset'] = ($view->page - 1) * 20;

        //retrieve page data
        $view->posts = get_posts($post_args);

        //retrieve total posts
        $count_args = array();
        if ($view->search)
            $count_args['name'] = $view->search;
        if ($view->category)
            $count_args['category'] = $view->category;
        if ($view->orderby)
            $count_args['orderby'] = $view->orderby;
        if ($view->order)
            $count_args['order'] = $view->order;
        if ($view->post_type) {
            $count_args['post_type'] = $view->post_type;
        } else {
            $count_args['post_type'] = $view->post_types;
        }
        $count_args['numberposts'] = 9999;
        $count_args['offset'] = 0;

        $view->total_posts = count(get_posts($count_args));
        $view->total_pages = ceil($view->total_posts / 20);

        $view->inline_js = "
            window.addEvent('domready', function() {
                $$('#post_type, #category, #post_status').addEvent('change', function(){
                    var rel = document.id(this.options[this.selectedIndex]).get('rel');
                    window.location.href = rel;
                });

                document.id('search-button').addEvent('click', function(){
                    var val = document.id('search-input').value;
                    var href = document.id('search-button').get('href');
                    document.id('search-button').set('href', href + '&search=' + val);
                });

                $$('.article-link').addEvent('click', function(e){
                    e.preventDefault();

                    var title = this.get('title');
                    var link = this.get('rel');
                    var id = this.get('pid');
                    var type = this.get('ptype');
                    var data = {
            			type: type,
            			id: id,
            			title: title,
            			link: link
            		};
                    window.parent.document.getElementById('slice-linkdata').value = title;
                    window.parent.document.getElementById('slice-link').value = JSON.encode(data);
                    window.parent.wpSelectArticle(data.id, data.title, data.type, link);
                    window.parent.tb_remove();
                });
            });
            ";


        $typelist = '<select class="post_type" id="post_type" name="post_type">';
        $typelist .= '<option rel="' . RokCommon_URL::updateParams($view->link, array("post_type" => '')) . '" value=""' . rg_selected('', $view->post_type, false) . '>' . 'Filter by Post Type' . '</option>';
        foreach ($view->post_types as $type) {
            $typelist .= '<option rel="' . RokCommon_URL::updateParams($view->link, array("post_type" => $type)) . '" value="' . $type . '"' . rg_selected($type, $view->post_type, false) . '>' . $type . '</option>';
        }
        $typelist .= '</select>';

        $view->typelist = $typelist;

        $type_args = array(
            'type' => 'post',
            'child_of' => 0,
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 1,
            'hierarchical' => 1,
            'taxonomy' => 'category',
            'pad_counts' => false);

        $view->categories = get_categories($type_args);

        $catlist = '<select class="category" id="category" name="category">';
        $catlist .= '<option rel="' . RokCommon_URL::updateParams($view->link, array("category" => "")) . '" value=""' . rg_selected("", $view->category, false) . '>' . 'Filter by Category' . '</option>';
        foreach ($view->categories as $cat) {
            $catlist .= '<option rel="' . RokCommon_URL::updateParams($view->link, array("category" => $cat->cat_ID)) . '" value="' . $cat->cat_ID . '"' . rg_selected($cat->cat_ID, $view->category, false) . '>' . $cat->name . '</option>';
        }
        $catlist .= '</select>';

        $view->catlist = $catlist;

        $publist = '<select class="post_status" id="post_status" name="post_status">';
        $publist .= '<option rel="' . RokCommon_URL::updateParams($view->link, array("post_status" => "")) . '" value=""' . rg_selected('', $view->post_status, false) . '>' . 'Filter by Status' . '</option>';
        $publist .= '<option rel="' . RokCommon_URL::updateParams($view->link, array("post_status" => "publish")) . '" value="publish"' . rg_selected('publish', $view->post_status, false) . '>' . 'Published' . '</option>';
        $publist .= '<option rel="' . RokCommon_URL::updateParams($view->link, array("post_status" => "unpublish")) . '" value="unpublish"' . rg_selected('unpublish', $view->post_status, false) . '>' . 'Unpublished' . '</option>';
        $publist .= '</select>';

        $view->publist = $publist;

        $searchbox = '<input type="text" id="search-input" class="search-input" size="25" style="float:left;clear:none;" name="search" value="' . $view->search . '" />';
        $searchbox .= '<a href="'.$view->search_link.'" id="search-button" class="button ok"  style="float:left;clear:none;">Search by Name</a>';
        $searchbox .= '<a href="'.$view->base_link.'" id="clear-button" class="button cancel" style="float:left;clear:none;">Clear Filters</a>';

        $view->searchbox = $searchbox;

        return $view;
    }
}
