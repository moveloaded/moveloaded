<?php
 /**
  * @version   $Id: view.php 10867 2013-05-30 04:04:46Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */


class RokGallery_Views_Detail_View
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
        global $wp, $post;

        $session_namespace = 'rokgallery.site';

        $view = new stdClass();

        //get post vars
        $id = RokGallery_Request::getString('id', '');

        //get instance variables
        $style = ($instance['style']) ? $instance['style'] : $instance['default_style'];
        $post_id = (RokGallery_Request::getString('rokgallery', $wp->query_vars['rokgallery']));

        // Get session variables
        $layout = (RokGallery_Request::getString('layout')) ? RokGallery_Request::getString('layout') : rg_get_session_var('layout', 'default', $session_namespace);
        if ($layout == 'default') {
            $layout = $instance['detail_layout'];
        }

        $items_per_row = ((int)$instance[$layout . '-items_per_row']) ? (int)$instance[$layout . '-items_per_row'] : 2;
        $rows_per_page = ((int)$instance[$layout . '-rows_per_page']) ? (int)$instance[$layout . '-rows_per_page'] : 2;

        $gallery_id = rg_get_session_var('gallery_id', $instance['gallery_id'], $session_namespace);
        $sort_by = rg_get_session_var('sort_by', $instance['default_sort_by'], $session_namespace);
        $sort_direction = rg_get_session_var('sort_direction', $instance['default_sort_direction'], $session_namespace);
        $page = (RokGallery_Request::getString('page')) ? RokGallery_Request::getString('page') : rg_get_session_var('last_page', 1, $session_namespace);
        $items_per_page = rg_get_session_var('items_per_page', $items_per_row * $rows_per_page, $session_namespace);


        /** @var RokGallery_Site_DetailModel $rtmodel  */
        $view->rtmodel = new RokGallery_Site_DetailModel($gallery_id, $id, $page, $items_per_page, $sort_by, $sort_direction);
        $slice = $view->rtmodel->getSingle();
        if ($slice === false) {
            //return JError::raiseError(500, 'Gallery Item is not published.');
        }
        if (!RokCommon_Session::get('rokgallery.site.views.file_' . $slice->file_id, false)) {
            $slice->incrementView();
            RokCommon_Session::set('rokgallery.site.views.file_' . $slice->file_id, true);
        }
        $nonce = wp_create_nonce('rokgallery-ajax-nonce');
        //$path = htmlentities(ABSPATH);

        $base_page_url = RokCommon_URL::setParams(get_permalink( $post->ID ),
                                                    array(
                                                        'rokgallery' => $post_id,
                                                        'layout' => $layout,
                                                        'sort_by' => $sort_by,
                                                        'sort_direction' => $sort_direction,
                                                        'view' => 'gallery',
                                                        'page' => $page
                                                    ));

        $base_ajax_url = get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action=rokgallery&nonce='.$nonce;

        // Assignments to JS namespaces
        $view->base_ajax_url = $base_ajax_url;

        $next_link = null;
        $prev_link = null;
        if ($view->rtmodel->getNextId() != null) {
            $next_link = RokCommon_URL::updateParams($base_page_url, array('view' => 'detail', 'id' => $view->rtmodel->getNextId(), 'page' => $view->rtmodel->getNextPage()));
        }
        if ($view->rtmodel->getPrevId() != null) {
            $prev_link = RokCommon_URL::updateParams($base_page_url, array('view' => 'detail', 'id' => $view->rtmodel->getPrevId(), 'page' => $view->rtmodel->getPrevPage()));
        }

        //populate context paths
        $base_context = 'rokgallery.detail';
        $context = $base_context . ".default";
        $style_context = $context . "." . $style;

        $view->gallery_link = $base_page_url;
        $view->gallery_name = $instance['post_title'];
        $view->base_context = $base_context;
        $view->context = $context;
        $view->style_context = $style_context;
        $view->width = $slice->Gallery->width;
        $view->height = $slice->Gallery->height;
        $view->love_text = rc__(RokGallery_Config::getOption(RokGallery_Config::OPTION_LOVE_TEXT));
        $view->unlove_text = rc__(RokGallery_Config::getOption(RokGallery_Config::OPTION_UNLOVE_TEXT));
        $view->slice = $slice;
        $image = self::getPresentationDetailImage($slice, $instance);
        $view->image = $image;

        rg_set_session_var('last_page', $view->rtmodel->getCurrentPage(), $session_namespace);

        $view->next_link = $next_link;
        $view->prev_link = $prev_link;
        $view->show_title = $instance['detail_show_title'];
        $view->show_caption = $instance['detail_show_caption'];
        $view->show_tags = $instance['detail_show_tags'];
        $view->show_tags_count = $instance['detail_show_tags_count'];
        $view->show_created_at = $instance['detail_show_created_at'];
        $view->show_updated_at = $instance['detail_show_updated_at'];
        $view->show_loves = $instance['detail_show_loves'];
        $view->show_views = $instance['detail_show_views'];
        $view->show_filesize = $instance['detail_show_filesize'];
        $view->show_dimensions = $instance['detail_show_dimensions'];
        $view->show_download_full = $instance['detail_show_download_full'];
        $view->show_gallery_info = $instance['detail_show_download_full'];

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

}