<?php
/**
 * @version   $Id: posttype_form.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

//load custom field classes
?>
<!-- Begin RokGallery MetaBox Form -->
<script type="text/javascript">
    window.addEvent('domready', function () {
        new Tips(".rok-tips", {title:"data-tips"});
    });
    jQuery(document).ready(function($){
        $("#rok-tabs").tabs();
    });
</script>
<div class="inside" id="rg-content">

<div id="rok-tabs">
	<ul class="rok-tabs">
		<li class="rok-tabs"><a href="#tab-1"><span><?php rc_e('ROKGALLERY_GALLERY_VIEW_GALLERY_OPTIONS');?></span></a></li>
		<li class="rok-tabs"><a href="#tab-2"><span><?php rc_e('ROKGALLERY_GALLERY_VIEW_LAYOUT_OPTIONS');?></span></a></li>
		<li class="rok-tabs"><a href="#tab-3"><span><?php rc_e('ROKGALLERY_GALLERY_VIEW_GALLERY_VIEW_OPTIONS');?></span></a></li>
		<li class="rok-tabs"><a href="#tab-4"><span><?php rc_e('ROKGALLERY_GALLERY_VIEW_DETAIL_VIEW_OPTIONS');?></span></a></li>
	</ul>

	<!-- Tab 1 -->
	<div id="tab-1">
		<table class="rokgallery-options-table">
			<thead></thead>
			<tbody>
				<tr class="">
					<td class="rokgallery-table-25">
						<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_GALLERY'); ?>" for="gallery_id"><?php rc_e('ROKGALLERY_LABEL_GALLERY'); ?></label>
					</td>
					<td class="rokgallery-table-75">
						<?php echo RokGallery_Forms_Fields_GalleryManager::getInput($instance, 'gallery_id', 'gallery_id', $instance['gallery_id'], 'title');?>
					</td>
				</tr>
				<tr class="">
					<td class="rokgallery-table-25">
						<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_DEFAULT_LAYOUT'); ?>" for="default_layout"><?php rc_e('ROKGALLERY_LABEL_DEFAULT_LAYOUT'); ?></label>
					</td>
					<td class="rokgallery-table-75">
						<select class="rokgallery-fields" id="default_layout" name="default_layout">
							<option value="grid-3col"<?php rg_selected($instance['default_layout'], 'grid-3col'); ?>><?php rc_e('ROKGALLERY_GRID_3COL'); ?></option>
							<option value="grid-4col"<?php rg_selected($instance['default_layout'], 'grid-4col'); ?>><?php rc_e('ROKGALLERY_GRID_4COL'); ?></option>
							<option value="list-2col"<?php rg_selected($instance['default_layout'], 'list-2col'); ?>><?php rc_e('ROKGALLERY_LIST_2COL'); ?></option>
						</select>
					</td>
				</tr>
				<tr class="">
					<td class="rokgallery-table-25">
						<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_STYLE'); ?>" for="default_style"><?php rc_e('ROKGALLERY_LABEL_STYLE'); ?> </label>
					</td>
					<td class="rokgallery-table-75">
						<select class="rokgallery-fields" id="default_style" name="default_style">
							<option value="light"<?php rg_selected($instance['default_style'], 'light'); ?>><?php rc_e('ROKGALLERY_LIGHT'); ?></option>
							<option value="dark"<?php rg_selected($instance['default_style'], 'dark'); ?>><?php rc_e('ROKGALLERY_DARK'); ?></option>
						</select>
					</td>
				</tr>
				<tr class="">
					<td class="rokgallery-table-25">
						<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SORT_BY'); ?>" for="default_sort_by"><?php rc_e('ROKGALLERY_LABEL_SORT_BY'); ?></label>
					</td>
					<td class="rokgallery-table-75">
						<select class="rokgallery-fields" id="default_sort_by"
								name="default_sort_by">
							<option value="gallery_ordering"<?php rg_selected($instance['default_sort_by'], 'gallery_ordering'); ?>><?php rc_e('ROKGALLERY_SORT_GALLERY_ORDERING'); ?></option>
							<option value="slice_title"<?php rg_selected($instance['default_sort_by'], 'slice_title'); ?>><?php rc_e('ROKGALLERY_SORT_TITLE'); ?></option>
							<option value="slice_updated_at"<?php rg_selected($instance['default_sort_by'], 'slice_updated_at'); ?>><?php rc_e('ROKGALLERY_SORT_UPDATED'); ?></option>
							<option value="file_created_at"<?php rg_selected($instance['default_sort_by'], 'file_created_at'); ?>><?php rc_e('ROKGALLERY_SORT_CREATED'); ?></option>
							<option value="loves"<?php rg_selected($instance['default_sort_by'], 'loves'); ?>><?php rc_e('ROKGALLERY_SORT_LOVES'); ?></option>
							<option value="views"<?php rg_selected($instance['default_sort_by'], 'views'); ?>><?php rc_e('ROKGALLERY_SORT_VIEWS'); ?></option>
							<option value="random"<?php rg_selected($instance['default_sort_by'], 'random'); ?>><?php rc_e('ROKGALLERY_SORT_RANDOM'); ?></option>
						</select>
					</td>
				</tr>
				<tr class="">
					<td class="rokgallery-table-25">
						<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SORT_DIRECTION'); ?>" for="default_sort_direction"><?php rc_e('ROKGALLERY_LABEL_SORT_DIRECTION'); ?></label>
					</td>
					<td class="rokgallery-table-75">
						<select class="rokgallery-fields" id="default_sort_direction"
								name="default_sort_direction">
							<option value="ASC"<?php rg_selected($instance['default_sort_direction'], 'ASC'); ?>><?php rc_e('ROKGALLERY_ASCENDING'); ?></option>
							<option value="DESC"<?php rg_selected($instance['default_sort_direction'], 'DESC'); ?>><?php rc_e('ROKGALLERY_DESCENDING'); ?></option>
						</select>
					</td>
				</tr>
				<tr class="">
					<td class="rokgallery-table-25">
						<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_LINKTYPE'); ?>" for="slice_link_to"><?php rc_e('ROKGALLERY_LABEL_LINKTYPE'); ?></label>
					</td>
					<td class="rokgallery-table-75">
						<select class="rokgallery-fields" id="slice_link_to"
								name="slice_link_to">
							<option value="none"<?php rg_selected($instance['slice_link_to'], 'none'); ?>><?php rc_e('ROKGALLERY_LINK_NONE'); ?></option>
							<option value="slice_link"<?php rg_selected($instance['slice_link_to'], 'slice_link'); ?>><?php rc_e('ROKGALLERY_SLICE_LINK Link'); ?></option>
							<option value="rokbox"<?php rg_selected($instance['slice_link_to'], 'rokbox'); ?>><?php rc_e('ROKGALLERY_ROKBOX_LINK'); ?></option>
							<option value="rokbox_full"<?php rg_selected($instance['slice_link_to'], 'rokbox_full'); ?>><?php rc_e('ROKGALLERY_ROKBOX_LINK_FULL'); ?></option>
						</select>
					</td>
				</tr>
			</tbody>
			<tfoot></tfoot>
		</table>
	</div>

	<!-- Tab 2 -->
	<div id="tab-2">
		<table class="rokgallery-options-table">
			<thead></thead>
			<tbody>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_SORTS'); ?>"
						   for="show_sorts">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_SORTS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="show_sorts"
							name="show_sorts">
						<option value="1"<?php rg_selected($instance['show_sorts'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['show_sorts'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_AVAILABLE_SORTS'); ?>"
						   for="available_sorts">
						<?php rc_e('ROKGALLERY_LABEL_AVAILABLE_SORTS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="available_sorts"
							multiple="multiple" size="5"
							name="available_sorts[]">
						<option value="gallery_ordering"<?php rg_selected($instance['available_sorts'], 'gallery_ordering'); ?>><?php rc_e('ROKGALLERY_SORT_GALLERY_ORDERING'); ?></option>
						<option value="slice_title"<?php rg_selected($instance['available_sorts'], 'slice_title'); ?>><?php rc_e('ROKGALLERY_SORT_TITLE'); ?></option>
						<option value="slice_updated_at"<?php rg_selected($instance['available_sorts'], 'slice_updated_at'); ?>><?php rc_e('ROKGALLERY_SORT_UPDATED'); ?></option>
						<option value="file_created_at"<?php rg_selected($instance['available_sorts'], 'file_created_at'); ?>><?php rc_e('ROKGALLERY_SORT_CREATED'); ?></option>
						<option value="loves"<?php rg_selected($instance['available_sorts'], 'loves'); ?>><?php rc_e('ROKGALLERY_SORT_LOVES'); ?></option>
						<option value="views"<?php rg_selected($instance['available_sorts'], 'views'); ?>><?php rc_e('ROKGALLERY_SORT_VIEWS'); ?></option>
						<option value="random"<?php rg_selected($instance['available_sorts'], 'random'); ?>><?php rc_e('ROKGALLERY_SORT_RANDOM'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_AVAILABLE_LAYOUTS'); ?>"
						   for="show_available_layouts">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_AVAILABLE_LAYOUTS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="show_available_layouts"
							name="show_available_layouts">
						<option value="1"<?php rg_selected($instance['show_available_layouts'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['show_available_layouts'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_AVAILABLE_LAYOUTS'); ?>"
						   for="available_layouts">
						<?php rc_e('ROKGALLERY_LABEL_AVAILABLE_LAYOUTS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields"
							id="available_layouts" multiple="multiple" size="3"
							name="available_layouts[]">
						<option value="grid-3col"<?php rg_selected($instance['available_layouts'], 'grid-3col'); ?>><?php rc_e('ROKGALLERY_GRID_3COL'); ?></option>
						<option value="grid-4col"<?php rg_selected($instance['available_layouts'], 'grid-4col'); ?>><?php rc_e('ROKGALLERY_GRID_4COL'); ?></option>
						<option value="list-2col"<?php rg_selected($instance['available_layouts'], 'list-2col'); ?>><?php rc_e('ROKGALLERY_LIST_2COL'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_LABEL_NUM_PAGES_SHOWN_IN_RANGE'); ?>"
						   for="pages_in_shown_range">
						<?php rc_e('ROKGALLERY_LABEL_NUM_PAGES_SHOWN_IN_RANGE'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<?php echo RokGallery_Forms_Fields_Integer::getInput('pages_in_shown_range', 'pages_in_shown_range', $instance['pages_in_shown_range'], 1, 21, 1)?>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_ROWS_ON_3COL_GRID_PAGE'); ?>"
						   for="grid-3col-rows_per_page">
						<?php rc_e('Rows on Grid 3 Column Page'); ?>
					</label>

				</td>
				<td class="rokgallery-table-75">
					<?php echo RokGallery_Forms_Fields_Integer::getInput('grid-3col-rows_per_page', 'grid-3col-rows_per_page', $instance['grid-3col-rows_per_page'], 1, 20, 1)?>
					<input name="grid-3col-items_per_row" type="hidden" value="3"/>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_ROWS_ON_4COL_GRID_PAGE'); ?>"
						   for="grid-4col-rows_per_page">
						<?php rc_e('ROKGALLERY_LABEL_ROWS_ON_4COL_GRID_PAGE'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<?php echo RokGallery_Forms_Fields_Integer::getInput('grid-4col-rows_per_page', 'grid-4col-rows_per_page', $instance['grid-4col-rows_per_page'], 1, 20, 1)?>
					<input name="grid-4col-items_per_row" type="hidden" value="4"/>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_ROWS_ON_2COL_LIST_PAGE'); ?>"
						   for="list-2col-rows_per_page">
						<?php rc_e('ROKGALLERY_LABEL_ROWS_ON_2COL_LIST_PAGE'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<?php echo RokGallery_Forms_Fields_Integer::getInput('list-2col-rows_per_page', 'list-2col-rows_per_page', $instance['list-2col-rows_per_page'], 1, 20, 1)?>
					<input name="list-2col-items_per_row" type="hidden" value="2"/>
				</td>
			</tr>
			</tbody>
			<tfoot></tfoot>
		</table>
	</div>

	<!-- Tab 3 -->
	<div id="tab-3">
		<table class="rokgallery-options-table">
			<thead></thead>
			<tbody>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_TITLE'); ?>"
						   for="gallery_show_title">
						<?php rc_e('ROKGALLERY_LABEL_TITLE'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_show_title"
							name="gallery_show_title">
						<option value="1"<?php rg_selected($instance['gallery_show_title'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['gallery_show_title'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_USE_TITLE_FROM'); ?>"
						   for="gallery_use_title_from">
						<?php rc_e('ROKGALLERY_LABEL_USE_TITLE_FROM'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_use_title_from"
							name="gallery_use_title_from">
						<option value="file"<?php rg_selected($instance['gallery_use_title_from'], 'file'); ?>><?php rc_e('ROKGALLERY_FILE'); ?></option>
						<option value="slice"<?php rg_selected($instance['gallery_use_title_from'], 'slice'); ?>><?php rc_e('ROKGALLERY_SLICE'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_CAPTION'); ?>"
						   for="gallery_show_caption">
						<?php rc_e('ROKGALLERY_LABEL_CAPTION'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_show_caption"
							name="gallery_show_caption">
						<option value="1"<?php rg_selected($instance['gallery_show_caption'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['gallery_show_caption'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_USE_CAPTION_FROM'); ?>"
						   for="gallery_use_caption_from">
						<?php rc_e('ROKGALLERY_LABEL_USE_CAPTION_FROM'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_use_caption_from"
							name="gallery_use_caption_from">
						<option value="file"<?php rg_selected($instance['detail_use_caption_from'], 'file'); ?>><?php rc_e('ROKGALLERY_FILE'); ?></option>
						<option value="slice"<?php rg_selected($instance['detail_use_caption_from'], 'slice'); ?>><?php rc_e('ROKGALLERY_SLICE'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_TAGS'); ?>"
						   for="gallery_show_tags">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_TAGS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_show_tags"
							name="gallery_show_tags">
						<option value="1"<?php rg_selected($instance['gallery_show_tags'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['gallery_show_tags'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_USE_TAGS_FROM'); ?>"
						   for="gallery_use_tags_from">
						<?php rc_e('ROKGALLERY_LABEL_USE_TAGS_FROM'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_use_tags_from"
							name="gallery_use_tags_from">
						<option value="file"<?php rg_selected($instance['gallery_use_tags_from'], 'file'); ?>><?php rc_e('ROKGALLERY_FILE'); ?></option>
						<option value="slice"<?php rg_selected($instance['gallery_use_tags_from'], 'slice'); ?>><?php rc_e('ROKGALLERY_SLICE'); ?></option>
						<option value="combined"<?php rg_selected($instance['gallery_use_tags_from'], 'combined'); ?>><?php rc_e('ROKGALLERY_COMBINED'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_REMOVE_GALLERY_TAGS'); ?>"
						   for="gallery_remove_gallery_tags">
						<?php rc_e('ROKGALLERY_LABEL_REMOVE_GALLERY_TAGS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields"
							id="gallery_remove_gallery_tags"
							name="gallery_remove_gallery_tags">
						<option value="1"<?php rg_selected($instance['gallery_remove_gallery_tags'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['gallery_remove_gallery_tags'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_TAGS_COUNT'); ?>"
						   for="gallery_show_tags_count">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_TAGS_COUNT'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_show_tags_count"
							name="gallery_show_tags_count">
						<option value="1"<?php rg_selected($instance['gallery_show_tags_count'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['gallery_show_tags_count'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_CREATED_DATE'); ?>"
						   for="gallery_show_created_at">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_CREATED_DATE'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_show_created_at"
							name="gallery_show_created_at">
						<option value="1"<?php rg_selected($instance['gallery_show_created_at'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['gallery_show_created_at'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_LOVES'); ?>"
						   for="gallery_show_loves">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_LOVES'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_show_loves"
							name="gallery_show_loves">
						<option value="1"<?php rg_selected($instance['gallery_show_loves'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['gallery_show_loves'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_VIEWS'); ?>"
						   for="gallery_show_views">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_VIEWS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="gallery_show_views"
							name="gallery_show_views">
						<option value="1"<?php rg_selected($instance['gallery_show_views'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['gallery_show_views'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">

				</td>
				<td class="rokgallery-table-75">

				</td>
			</tr>
			</tbody>
			<tfoot></tfoot>
		</table>
	</div>

	<!-- Tab 4 -->
	<div id="tab-4">
		<table class="rokgallery-options-table">
			<thead></thead>
			<tbody>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_TITLE'); ?>"
						   for="detail_show_title">
						<?php rc_e('ROKGALLERY_LABEL_TITLE'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_title"
							name="detail_show_title">
						<option value="1"<?php rg_selected($instance['detail_show_title'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_title'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_USE_TITLE_FROM'); ?>"
						   for="detail_use_title_from">
						<?php rc_e('ROKGALLERY_LABEL_USE_TITLE_FROM'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_use_title_from"
							name="detail_use_title_from">
						<option value="file"<?php rg_selected($instance['detail_use_title_from'], 'file'); ?>><?php rc_e('ROKGALLERY_FILE'); ?></option>
						<option value="slice"<?php rg_selected($instance['detail_use_title_from'], 'slice'); ?>><?php rc_e('ROKGALLERY_SLICE'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_CAPTION'); ?>"
						   for="detail_show_caption">
						<?php rc_e('ROKGALLERY_LABEL_CAPTION'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_caption"
							name="detail_show_caption">
						<option value="1"<?php rg_selected($instance['detail_show_caption'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_caption'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_USE_CAPTION_FROM'); ?>"
						   for="detail_use_caption_from">
						<?php rc_e('ROKGALLERY_LABEL_USE_CAPTION_FROM'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_use_caption_from"
							name="detail_use_caption_from">
						<option value="file"<?php rg_selected($instance['detail_use_caption_from'], 'file'); ?>><?php rc_e('ROKGALLERY_FILE'); ?></option>
						<option value="slice"<?php rg_selected($instance['detail_use_caption_from'], 'slice'); ?>><?php rc_e('ROKGALLERY_SLICE'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_TAGS'); ?>"
						   for="detail_show_tags">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_TAGS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_tags"
							name="detail_show_tags">
						<option value="1"<?php rg_selected($instance['detail_show_tags'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_tags'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_USE_TAGS_FROM'); ?>"
						   for="detail_remove_gallery_tags">
						<?php rc_e('ROKGALLERY_LABEL_USE_TAGS_FROM'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_use_tags_from"
							name="detail_use_tags_from">
						<option value="file"<?php rg_selected($instance['detail_use_tags_from'], 'file'); ?>><?php rc_e('ROKGALLERY_FILE'); ?></option>
						<option value="slice"<?php rg_selected($instance['detail_use_tags_from'], 'slice'); ?>><?php rc_e('ROKGALLERY_SLICE'); ?></option>
						<option value="combined"<?php rg_selected($instance['detail_use_tags_from'], 'combined'); ?>><?php rc_e('ROKGALLERY_COMBINED'); ?></option>

					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_REMOVE_GALLERY_TAGS'); ?>"
						   for="detail_remove_gallery_tags">
						<?php rc_e('ROKGALLERY_LABEL_REMOVE_GALLERY_TAGS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_remove_gallery_tags"
							name="detail_remove_gallery_tags">
						<option value="1"<?php rg_selected($instance['detail_remove_gallery_tags'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_remove_gallery_tags'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_TAGS_COUNT'); ?>"
						   for="detail_show_tags_count">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_TAGS_COUNT'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_tags_count"
							name="detail_show_tags_count">
						<option value="1"<?php rg_selected($instance['detail_show_tags_count'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_tags_count'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_CREATED_DATE'); ?>"
						   for="detail_show_created_at">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_CREATED_DATE'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_created_at"
							name="detail_show_created_at">
						<option value="1"<?php rg_selected($instance['detail_show_created_at'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_created_at'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_UPDATED_DATE'); ?>"
						   for="detail_show_updated_at">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_UPDATED_DATE'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_updated_at"
							name="detail_show_updated_at">
						<option value="1"<?php rg_selected($instance['detail_show_updated_at'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_updated_at'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_LOVES'); ?>"
						   for="detail_show_loves">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_LOVES'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_loves"
							name="detail_show_loves">
						<option value="1"<?php rg_selected($instance['detail_show_loves'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_loves'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_VIEWS'); ?>"
						   for="detail_show_views">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_VIEWS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_views"
							name="detail_show_views">
						<option value="1"<?php rg_selected($instance['detail_show_views'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_views'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_FILESIZE'); ?>"
						   for="detail_show_filesize">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_FILESIZE'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_filesize"
							name="detail_show_filesize">
						<option value="1"<?php rg_selected($instance['detail_show_filesize'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_filesize'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_USE_FILESIZE_FROM'); ?>"
						   for="detail_use_filesize_from">
						<?php rc_e('ROKGALLERY_LABEL_USE_FILESIZE_FROM'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_use_filesize_from"
							name="detail_use_filesize_from">
						<option value="file"<?php rg_selected($instance['detail_use_filesize_from'], 'file'); ?>><?php rc_e('ROKGALLERY_FILE'); ?></option>
						<option value="slice"<?php rg_selected($instance['detail_use_filesize_from'], 'slice'); ?>><?php rc_e('ROKGALLERY_SLICE'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SLICE_DIMENSIONS'); ?>"
						   for="detail_show_dimensions">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_DIMENSIONS'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_dimensions"
							name="detail_show_dimensions">
						<option value="1"<?php rg_selected($instance['detail_show_dimensions'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_dimensions'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_USE_DIMENSIONS_FROM'); ?>"
						   for="detail_use_dimensions_from">
						<?php rc_e('ROKGALLERY_LABEL_USE_DIMENSIONS_FROM'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_use_dimensions_from"
							name="detail_use_dimensions_from">
						<option value="file"<?php rg_selected($instance['detail_use_dimensions_from'], 'file'); ?>><?php rc_e('ROKGALLERY_FILE'); ?></option>
						<option value="slice"<?php rg_selected($instance['detail_use_dimensions_from'], 'slice'); ?>><?php rc_e('ROKGALLERY_SLICE'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_DOWNLOAD_FULL'); ?>"
						   for="detail_show_download_full">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_DOWNLOAD_FULL'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_download_full"
							name="detail_show_download_full">
						<option value="1"<?php rg_selected($instance['detail_show_download_full'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_download_full'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td class="rokgallery-table-25">
					<label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOW_GALLERY_INFO'); ?>"
						   for="detail_show_gallery_info">
						<?php rc_e('ROKGALLERY_LABEL_SHOW_GALLERY_INFO'); ?>
					</label>
				</td>
				<td class="rokgallery-table-75">
					<select class="rokgallery-fields" id="detail_show_gallery_info"
							name="detail_show_gallery_info">
						<option value="1"<?php rg_selected($instance['detail_show_gallery_info'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
						<option value="0"<?php rg_selected($instance['detail_show_gallery_info'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
					</select>
				</td>
			</tr>
			</tbody>
			<tfoot></tfoot>
		</table>
		<input type="hidden" name="rokgallery_noncename" id="rokgallery_noncename" value="<?php wp_create_nonce('rt_rokgallery');?>"/>
	</div>
</div>

<div style="clear:both;"></div>
<!-- End RokGallery MetaBox Form -->