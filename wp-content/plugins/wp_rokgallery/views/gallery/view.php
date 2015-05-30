<?php
 /**
  * @version   $Id: view.php 10867 2013-05-30 04:04:46Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */


class RokGallery_Views_Gallery_View
{

    /**
     * @param null $instance
     * @return \stdClass
     */
    public function getView($instance = null)
    {
        global $wp, $post;

        $session_namespace = 'rokgallery.site';

        $view = new stdClass();

        //get post vars
        $sort_direction = RokGallery_Request::getString('sort_direction', '');
        $sort_by = RokGallery_Request::getString('sort_by', '');
        $layout = RokGallery_Request::getString('layout', '');

        //TODO get current menu item
        $menu_item = '';//JRequest::getInt('Itemid');

        //get instance variables
        $style = ($instance['style']) ? $instance['style'] : $instance['default_style'];
        $post_id = (RokGallery_Request::getString('rokgallery', $wp->query_vars['rokgallery']));

        // Get session variables
        $sort_direction = ($sort_direction) ? $sort_direction : rg_get_session_var('sort_direction', $instance['default_sort_direction'], $session_namespace);

        if ($instance['show_sorts']) {
            $sort_by = ($sort_by) ? $sort_by : rg_get_session_var('sort_by', $instance['default_sort_by'], $session_namespace);
        }
        else {
            $sort_by = ($sort_by) ? $sort_by : $instance['default_sort_by'];
        }

        if ($instance['show_available_layouts'])
            $layout = ($layout) ? $layout : rg_get_session_var('layout', $instance['default_layout'], $session_namespace);
        else
            $layout = ($layout) ? $layout : $instance['default_layout'];
        if ($layout == 'default') {
            $layout = $instance['default_layout'];
        }

        $gallery_id = $instance['gallery_id'];
        $items_per_row = ((int)$instance[$layout . '-items_per_row']) ?  (int)$instance[$layout . '-items_per_row']: 2;
        $rows_per_page = ((int)$instance[$layout . '-rows_per_page']) ? (int)$instance[$layout . '-rows_per_page'] : 2;
        $items_per_page = $items_per_row * $rows_per_page;
        $current_page = ($wp->query_vars['page'] != null) ? str_replace('/', '', $wp->query_vars['page']) : ((RokGallery_Request::getInt('page')) ? RokGallery_Request::getInt('page') : 1);
        $nonce = wp_create_nonce('rokgallery-ajax-nonce');

        // Set session passed vars
        rg_set_session_var('gallery_id', $gallery_id, $session_namespace);
        rg_set_session_var('sort_by', $sort_by, $session_namespace);
        rg_set_session_var('sort_direction', $sort_direction, $session_namespace);
        rg_set_session_var('layout', $layout, $session_namespace);
        rg_set_session_var('last_page', $current_page, $session_namespace);
        rg_set_session_var('items_per_page', $items_per_page, $session_namespace);
        rg_set_session_var('rokgallery', $post_id, $session_namespace);

        $base_page_url = RokCommon_URL::setParams(get_permalink( $post->ID ),
                                                  array(
                                                      'rokgallery' => $post_id,
                                                      'layout' => $layout,
                                                      'sort_by' => $sort_by,
                                                      'sort_direction' => $sort_direction,
                                                  ));

        $base_ajax_url = get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action=rokgallery&nonce='.$nonce;

        $model = new RokGallery_Site_GalleryModel($gallery_id, $sort_by, $sort_direction);


        $gallery = RokGallery_Model_GalleryTable::getSingle($gallery_id);
        if ($gallery == false) {
            //return JError::raiseError(500, 'Gallery with ID "' . $instance['gallery_id'] . '" not found');
        }

        $slices = $model->getPagedSlices($current_page, $items_per_page);
        $pager = $model->getPager();


        if ($pager->getLastPage() < $current_page) $current_page = $pager->getLastPage();

        $next_page = false;
        $prev_page = false;
        $pages = false;
        if ($pager->haveToPaginate()) {
            $ranger = $pager->getRange('Sliding', array('chunk' => $instance['pages_in_shown_range']));
            $pages = array();
            foreach ($ranger->rangeAroundPage() as $page)
            {
                $page_class = new stdClass();
                $page_class->page_num = $page;
                $page_class->active = ($current_page == $page);
                if (!$page_class->active) {
                    $page_class->link = RokCommon_URL::updateParams($base_page_url, array('page' => $page));
                }
                else {
                    $page_class->link = '#';
                }
                $pages[] = $page_class;
                if ($page == $pager->getNextPage() && $pager->getNextPage() != $current_page) {
                    $next_page = $page_class;
                }
                if ($page == $pager->getPreviousPage() && $pager->getPreviousPage() != $current_page) {
                    $prev_page = $page_class;
                }
            }
        }

        $images = array();
        $passed_slices = array();
        foreach ($slices as &$slice)
        {
            $images[] = RokGallery_Views_Gallery_View::getPresentationGalleryImage($slice, $instance, $base_page_url, $sort_by, $sort_direction);
            $passed_slices[$slice->id] = $slice;
        }
        $view->images = $images;
        $view->slices = $passed_slices;

        $layout_names = array('grid-3col' => rc__('ROKGALLERY_GRID_3COL'),
                              'grid-4col' => rc__('ROKGALLERY_GRID_4COL'),
                              'list-2col' => rc__('ROKGALLERY_LIST_2COL'));
        $layouts = RokGallery_Views_Gallery_View::getList('layout', $layout, $current_page, $layout_names, $menu_item, $base_page_url);
        $view->layouts = $layouts;

        $style_names = array('light' => rc__('ROKGALLERY_LIGHT'),
                             'dark' => rc__('ROKGALLERY_DARK'));
        $styles = RokGallery_Views_Gallery_View::getList('style', $style, $current_page, $style_names, $menu_item, $base_page_url);
        $view->styles = $styles;

        $sort_by_names = array('gallery_ordering' => rc__('ROKGALLERY_SORT_GALLERY_ORDERING'),
                               'file_created_at' => rc__('ROKGALLERY_SORT_CREATED'),
                               'slice_updated_at' => rc__('ROKGALLERY_SORT_UPDATED'),
                               'slice_title' => rc__('ROKGALLERY_SORT_TITLE'),
                               'loves' => rc__('ROKGALLERY_SORT_LOVES'),
                               'views' => rc__('ROKGALLERY_SORT_VIEWS'));
        $sort_bys = RokGallery_Views_Gallery_View::getList('sort_by', $sort_by, $current_page, $sort_by_names, $menu_item, $base_page_url);
        $view->sort_bys = $sort_bys;

        $sort_dir_names = array('ASC' => 'ascending', 'DESC' => 'descending');
        $sort_directions = RokGallery_Views_Gallery_View::getList('sort_direction', $sort_direction, $current_page, $sort_dir_names, $menu_item, $base_page_url);
        $view->sort_directions = $sort_directions;

        $total_items = $pager->getNumResults();
        $item_number = $pager->getFirstIndice();

        //populate context paths
        $base_context = 'rokgallery.gallery';
        $context = $base_context . "." . $layout;
        $style_context = $context . "." . $style;

        // Assignments to JS namespaces
        $view->base_ajax_url = $base_ajax_url;

        $view->available_layouts = (is_array($instance['available_layouts'])) ? $instance['available_layouts'] : explode(',', $instance['available_layouts']);
        $view->available_sorts = (is_array($instance['available_sorts'])) ? $instance['available_sorts'] :explode(',', $instance['available_sorts']);

        $sort_dir = false;
        foreach ($sort_bys as $sort_by_item)
        {
            if ($sort_by_item->active) {
                $sort_dir = new stdClass();
                $sort_dir->field = $sort_by_item->name;
                $other_sort = ($sort_direction == 'ASC') ? 'DESC' : 'ASC';
                $sort_dir->link = $sort_directions[$other_sort]->link;
                $sort_dir->class = ($other_sort == 'ASC') ? 'ascending' : 'descending';
                break;
            }
        }

        // Joomla params
        $view->show_page_heading = $instance['show_page_heading'];
        $view->page_heading = rg_escape($instance['page_heading']);

        // Assignments to page passed vars
        $view->pages = $pages;
        $view->next_page = $next_page;
        $view->prev_page = $prev_page;
        $view->rows_per_page = $rows_per_page;
        $view->items_per_row = $items_per_row;
        $view->total_items = $total_items;
        $view->items_per_page = $items_per_page;
        $view->item_number = $item_number;
        $view->base_context = $base_context;
        $view->context = $context;
        $view->style_context = $style_context;
        $view->current_page = $current_page;
        $view->thumb_width = $gallery->thumb_xsize;
        $view->thumb_height = $gallery->thumb_ysize;

        $view->sort_by = $sort_by;
        $view->sort_direction = $sort_direction;
        $view->sort_dir = $sort_dir;
        $view->style = $style;
        $view->layout = $layout;

        // populate basic page render vars
        $view->show_created_at = $instance['gallery_show_created_at'];
        $view->show_tags = $instance['gallery_show_tags'];
        $view->show_tags_count = $instance['gallery_show_tags_count'];
        $view->show_caption = $instance['gallery_show_caption'];
        $view->show_title = $instance['gallery_show_title'];
        $view->show_loves = $instance['gallery_show_loves'];
        $view->show_views = $instance['gallery_show_views'];
        $view->show_available_layouts = $instance['show_available_layouts'];
        $view->show_sorts = $instance['show_sorts'];

        return $view;

    }


    /**
     * @param RokGallery_Model_Slice $slice
     * @param $instance
     * @return stdClass
     */
    protected function &getPresentationDetailImage(RokGallery_Model_Slice $slice, $instance)
    {
        $image = new stdClass();
        $image->id = $slice->id;
        $image->title = ($instance['detail_use_title_from'] == 'slice') ? $slice->title
                : $slice->File->title;
        $image->caption = ($instance['detail_use_caption_from'] == 'slice') ? $slice->caption
                : $slice->File->description;
        $image->created_at = date('j M Y', strtotime($slice->File->created_at));
        $image->updated_at = date('j M Y', strtotime($slice->updated_at));
        $image->views = $slice->File->Views->count;
        $image->loves = $slice->File->Loves->count;
        $image->thumburl = $slice->thumburl;
        $image->imageurl = $slice->imageurl;
        $image->xsize = ($instance['detail_use_dimensions_from'] == 'slice') ? $slice->xsize
                : $slice->File->xsize;
        $image->ysize = ($instance['detail_use_dimensions_from'] == 'slice') ? $slice->ysize
                : $slice->File->ysize;

        $image->filesize = RokGallery_Helper::decodeSize(($instance['detail_use_filesize_from'] == 'slice')
                                                                 ? $slice->filesize
                                                                 : $slice->File->filesize);
        $image->fullimageurl = $slice->File->imageurl;
        $image->doilove = $slice->doilove;


        switch ($instance['gallery_use_tags_from']) {
            case 'slice':
                $tags =& $slice->Tags;
                break;
            case 'file':
                $tags =& $slice->File->Tags;
                break;
            case 'combined':
                $tags =& $slice->getCombinedTags();
                break;
        }

        $image->tags = array();
        foreach ($tags as $tag)
        {
            if (!($instance['gallery_remove_gallery_tags'] && in_array($tag['tag'], $slice->Gallery->filetags))) {
                $image->tags[] = $tag['tag'];
            }
        }

        return $image;
    }

    /**
     * @param $type
     * @param $active
     * @param $current_page
     * @param $items
     * @param $menu_item
     * @param $base_url
     * @return array
     */
    protected function getList($type, $active, $current_page, $items, $menu_item, $base_url)
    {
        $ret = array();
        foreach ($items as $key => $label)
        {
            $item_class = new stdClass();
            $item_class->name = $key;
            $item_class->link = RokCommon_URL::updateParams($base_url, array($type => $key, 'page' => $current_page));//'Itemid' => $menu_item
            $item_class->active = ($key == $active);
            $item_class->label = $label;
            $ret[$key] = $item_class;
        }
        return $ret;
    }

    /**
     * @param RokGallery_Model_Slice $slice
     * @param $instance
     * @param $base_page_url
     * @param $sort_by
     * @param $sort_direction
     * @return stdClass
     */
    protected function &getPresentationGalleryImage(RokGallery_Model_Slice &$slice, $instance, $base_page_url, $sort_by, $sort_direction)
    {
        $image = new stdClass();
        $image->id = $slice->id;
        $image->title = ($instance['gallery_use_title_from'] == 'slice') ? $slice->title
                : $slice->File->title;
        $image->caption = ($instance['gallery_use_caption_from'] == 'slice') ? $slice->caption
                : $slice->File->description;
        $image->created_at = date('j M Y', strtotime($slice->File->created_at));
        $image->updated_at = date('j M Y', strtotime($slice->updated_at));
        $image->views = $slice->File->Views->count;
        $image->loves = $slice->File->Loves->count;
        $image->thumburl = $slice->thumburl;
        $image->xsize = $slice->xsize;
        $image->ysize = $slice->ysize;
        $image->doilove = $slice->doilove;
        $image->filesize = $slice->filesize;
        $image->imageurl = $slice->imageurl;
        $image->rel = '';

        if (!RokGallery_Link::isJson($slice->link)) {
            $link = new RokGallery_Link(json_encode(new RokGallery_Link_Type_Manual_Info($slice->link)));
        }

        else {
            $link = new RokGallery_Link($slice->link);
        }


        switch ($instance['slice_link_to'])
        {
            case 'rokbox':
                $image->link = $slice->imageurl;
                $image->rel = 'rel="rokbox[' . $image->xsize . ' ' . $image->ysize . '](' . str_replace(' ', '', $slice->Gallery->name) . ')" title="' . $image->title . ' :: ' . $image->caption . '" ';
                break;
            case 'rokbox_full':
                $image->link = $slice->imageurl;
                $image->rel = 'rel="rokbox[' . $image->xsize . ' ' . $image->ysize . '](' . str_replace(' ', '', $slice->Gallery->name) . ')" title="' . $image->title . ' :: ' . $image->caption . '" ';
                break;
            case 'force_details':
                $image->link = RokCommon_URL::updateParams($base_page_url, array('view' => 'detail', 'id' => $slice->id));
                break;
            default:
                switch ($link->getType()) {
                    case 'manual':
                        $image->link = ($link->getUrl() != '') ? $link->getUrl()
                                : RokCommon_URL::updateParams($base_page_url, array('view' => 'detail', 'id' => $slice->id));
                        break;
                    case 'article':
                        $image->link = $link->getUrl();
                        break;
                }
                break;
        }

        switch ($instance['gallery_use_tags_from']) {
            case 'slice':
                $tags =& $slice->Tags;
                break;
            case 'file':
                $tags =& $slice->File->Tags;
                break;
            case 'combined':
                $tags =& $slice->getCombinedTags();
                break;
        }

        $image->tags = array();
        foreach ($tags as $tag)
        {
            if (!($instance['gallery_remove_gallery_tags'] && in_array($tag['tag'], $slice->Gallery->filetags))) {
                $image->tags[] = $tag['tag'];
            }
        }

        return $image;
    }

}