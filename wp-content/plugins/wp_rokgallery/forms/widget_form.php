<?php
/**
 * @version   $Id: widget_form.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

?>
<script type="text/javascript">
    if (typeof ROKWPSTUFF != 'undefined') ROKWPSTUFF = 1;
    else ROKWPSTUFF = 0;

    if (!ROKWPSTUFF){
    window.addEvent('domready', function () {
        new Tips(".rok-tips",{title:"data-tips"});

        var ajaxComplete = function(event, XMLHttpRequest, ajaxOptions){
            if (!ajaxOptions.data) return;
            var request = {}, pairs = ajaxOptions.data.split('&'), i, split, widget;

            Array.each(pairs, function(value, key){
                split = value.split('=');
                request[decodeURIComponent(split[0])] = decodeURIComponent(split[1]);
            });

            if((request.action && (request.action === 'save-widget')) || (request.gantry_action && (request.gantry_action == 'widgets-save'))){
             widget = jQuery('input.widget-id[value="' + request['widget-id'] + '"]').parents('.widget');
                var name = 'widget-' + request['widget-id'] + '-layout';
                    layout = request['widget-' + request['id_base'] + '['+request['multi_number']+'][layout]'];

                RokGalleryLayoutChanger.delay(500, RokGalleryLayoutChanger, [name, layout]);
                jQuery(document).trigger('saved_widget', widget[0]);
            }

        };

        window.addEvent('gantry-widgets', function(xhr, data){
            ajaxComplete(null, xhr, {data: data});
        });
        jQuery('#widgets-right').ajaxComplete(ajaxComplete);
    });

    var RokGalleryFixed = {},
        RokGalleryLayoutChanger = function(field, layout) {
        field = document.id(field);
        if (!field) return;

        var parent = content = field.getParent('.widget-content');

        var str = layout;
        content.getElements('.layout').setStyle('display','none');
        content.getElements('.'+str).setStyle('display','block');
        field.addEvent('change', function(){
            content = field.getParent('.widget-content');
            var sel = document.id(this.options[this.selectedIndex]).get('value'),
                rel = document.id(this.options[this.selectedIndex]).get('rel'),
                cleanid = content.getElement('.rg_gallery_id_id').id.replace('-', '_');
            RokGalleryFixed[cleanid] = rel == 'false' ? false : true;
            content.getElements('.layout').setStyle('display','none');
            content.getElements('.'+sel).setStyle('display','block');
        }).fireEvent('change');
    }
    }
</script>
<!-- Begin RokGallery Widget Admin -->

<div class="wrap" id="rg-content">

<p>
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_TITLE'); ?>"
           for="<?php echo rg_get_field_id($instance, 'title'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_TITLE'); ?>
    </label>
    <input class="widefat"
           id="<?php echo rg_get_field_id($instance, 'title'); ?>"
           name="<?php echo rg_get_field_name($instance, 'title'); ?>"
           type="text"
           value="<?php echo $instance['title']; ?>"/>
</p>

<p style="margin-bottom: 0;">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_GALLERY'); ?>"
           for="<?php echo rg_get_field_id($instance, 'allow_duplicate_files'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_GALLERY'); ?>
    </label>
    <?php echo RokGallery_Forms_Fields_GalleryManager::getInput($instance, rg_get_field_name($instance, 'gallery_id'), rg_get_field_id($instance, 'gallery_id'), $instance['gallery_id'], rg_get_field_id($instance, 'title'));?>
</p>

<p>
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_LINKTYPE'); ?>"
           for="<?php echo rg_get_field_id($instance, 'link', $instance); ?>">
        <?php rc_e('ROKGALLERY_LABEL_LINKTYPE'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'link'); ?>"
            name="<?php echo rg_get_field_name($instance, 'link', $instance); ?>">
        <option value="none"<?php rg_selected($instance['link'], 'none'); ?>><?php rc_e('ROKGALLERY_LINK_NONE'); ?></option>
        <option value="slice_link"<?php rg_selected($instance['link'], 'slice_link'); ?>><?php rc_e('ROKGALLERY_SLICE_LINK Link'); ?></option>
        <option value="rokbox"<?php rg_selected($instance['link'], 'rokbox'); ?>><?php rc_e('ROKGALLERY_ROKBOX_LINK'); ?></option>
        <option value="rokbox_full"<?php rg_selected($instance['link'], 'rokbox_full'); ?>><?php rc_e('ROKGALLERY_ROKBOX_LINK_FULL'); ?></option>
    </select>
</p>

<p>
    <label class="rok-tips" data-tips="<?php rc_e('DEFAULT_DESC_MENU_ITEM'); ?>"
           for="<?php echo rg_get_field_id($instance, 'default_menuitem'); ?>">
        <?php rc_e('DEFAULT_LABEL_MENU_ITEM'); ?>
    </label>
    <?php echo RokGallery_Forms_Fields_MenuItems::getInput(rg_get_field_name($instance, 'default_menuitem'), rg_get_field_id($instance, 'default_menuitem'), $instance['default_menuitem']);?>
</p>

<p>
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_TITLE'); ?>"
           for="<?php echo rg_get_field_id($instance, 'show_title'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_TITLE'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'show_title'); ?>"
            name="<?php echo rg_get_field_name($instance, 'show_title'); ?>">
        <option value="1"<?php rg_selected($instance['show_title'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
        <option value="0"<?php rg_selected($instance['show_title'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
    </select>
</p>

<p>
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_CAPTION'); ?>"
           for="<?php echo rg_get_field_id($instance, 'caption'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_CAPTION'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'caption'); ?>"
            name="<?php echo rg_get_field_name($instance, 'caption'); ?>">
        <option value="1"<?php rg_selected($instance['caption'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
        <option value="0"<?php rg_selected($instance['caption'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
    </select>
</p>

<p>
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SORT_BY'); ?>"
           for="<?php echo rg_get_field_id($instance, 'sort_by'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SORT_BY'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'sort_by'); ?>"
            name="<?php echo rg_get_field_name($instance, 'sort_by'); ?>">
        <option value="gallery_ordering"<?php rg_selected($instance['sort_by'], 'gallery_ordering'); ?>><?php rc_e('ROKGALLERY_SORT_GALLERY_ORDERING'); ?></option>
        <option value="slice_title"<?php rg_selected($instance['sort_by'], 'slice_title'); ?>><?php rc_e('ROKGALLERY_SORT_TITLE'); ?></option>
        <option value="slice_updated_at"<?php rg_selected($instance['sort_by'], 'slice_updated_at'); ?>><?php rc_e('ROKGALLERY_SORT_UPDATED'); ?></option>
        <option value="file_created_at"<?php rg_selected($instance['sort_by'], 'file_created_at'); ?>><?php rc_e('ROKGALLERY_SORT_CREATED'); ?></option>
        <option value="loves"<?php rg_selected($instance['sort_by'], 'loves'); ?>><?php rc_e('ROKGALLERY_SORT_LOVES'); ?></option>
        <option value="views"<?php rg_selected($instance['sort_by'], 'views'); ?>><?php rc_e('ROKGALLERY_SORT_VIEWS'); ?></option>
        <option value="random"<?php rg_selected($instance['sort_by'], 'random'); ?>><?php rc_e('ROKGALLERY_SORT_RANDOM'); ?></option>
    </select>
</p>

<p>
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SORT_DIRECTION'); ?>"
           for="<?php echo rg_get_field_id($instance, 'sort_direction'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SORT_DIRECTION'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'sort_direction'); ?>"
            name="<?php echo rg_get_field_name($instance, 'sort_direction'); ?>">
        <option value="ASC"<?php rg_selected($instance['sort_direction'], 'ASC'); ?>><?php rc_e('ROKGALLERY_ASCENDING'); ?></option>
        <option value="DESC"<?php rg_selected($instance['sort_direction'], 'DESC'); ?>><?php rc_e('ROKGALLERY_DESCENDING'); ?></option>
    </select>
</p>

<p>
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_LIMIT'); ?>"
           for="<?php echo rg_get_field_id($instance, 'limit_count'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_LIMIT'); ?>
    </label>
    <input class="widefat" id="<?php echo rg_get_field_id($instance, 'limit_count'); ?>"
           name="<?php echo rg_get_field_name($instance, 'limit_count'); ?>" type="text"
           value="<?php echo $instance['limit_count']; ?>"/>
</p>

<p>
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_STYLE'); ?>"
           for="<?php echo rg_get_field_id($instance, 'style'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_STYLE'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'style'); ?>"
            name="<?php echo rg_get_field_name($instance, 'style'); ?>">
        <option value="light"<?php rg_selected($instance['style'], 'light'); ?>><?php rc_e('ROKGALLERY_LIGHT'); ?></option>
        <option value="dark"<?php rg_selected($instance['style'], 'dark'); ?>><?php rc_e('ROKGALLERY_DARK'); ?></option>
    </select>
</p>

<p>
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_LAYOUT'); ?>"
           for="<?php echo rg_get_field_id($instance, 'layout'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_LAYOUT'); ?>
    </label>
    <?php echo RokGallery_Forms_Fields_Layout::getInput($instance, rg_get_field_name($instance, 'layout'), rg_get_field_id($instance, 'layout'), $instance['layout']);?>
</p>

<p class="grid layout">
     <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_COLUMNS'); ?>"
            for="<?php echo rg_get_field_id($instance, 'columns'); ?>">
         <?php rc_e('ROKGALLERY_LABEL_COLUMNS'); ?>
     </label>
     <?php echo RokGallery_Forms_Fields_Integer::getInput(rg_get_field_name($instance, 'columns'), rg_get_field_id($instance, 'columns'), $instance['columns'], 1, 10, 1);?>

 </p>


<p class="slideshow layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_ARROWS'); ?>"
           for="<?php echo rg_get_field_id($instance, 'arrows'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_ARROWS'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'arrows'); ?>"
            name="<?php echo rg_get_field_name($instance, 'arrows'); ?>">
        <option value="yes"<?php rg_selected($instance['arrows'], 'yes'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
        <option value="no"<?php rg_selected($instance['arrows'], 'no'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
        <option value="onhover"<?php rg_selected($instance['arrows'], 'onhover'); ?>><?php rc_e('ROKGALLERY_HOVER'); ?></option>
    </select>
</p>

<p class="slideshow layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_NAVIGATION'); ?>"
           for="<?php echo rg_get_field_id($instance, 'navigation'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_NAVIGATION'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'navigation'); ?>"
            name="<?php echo rg_get_field_name($instance, 'navigation'); ?>">
        <option value="thumbnails"<?php rg_selected($instance['navigation'], 'thumbnails'); ?>><?php rc_e('ROKGALLERY_THUMBNAILS'); ?></option>
        <option value="none"<?php rg_selected($instance['navigation'], 'none'); ?>><?php rc_e('ROKGALLERY_NONE'); ?></option>
    </select>
</p>

<p class="slideshow layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SLICEANIMATION_TYPE'); ?>"
           for="<?php echo rg_get_field_id($instance, 'animation_type'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SLICEANIMATION_TYPE'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'animation_type'); ?>"
            name="<?php echo rg_get_field_name($instance, 'animation_type'); ?>">
        <option value="random"<?php rg_selected($instance['animation_type'], 'random'); ?>><?php rc_e('ROKGALLERY_RANDOM'); ?></option>
        <option value="crossfade"<?php rg_selected($instance['animation_type'], 'crossfade'); ?>><?php rc_e('ROKGALLERY_CROSSFADE'); ?></option>
        <option value="blindsRight"<?php rg_selected($instance['animation_type'], 'blindsRight'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_RIGHT'); ?></option>
        <option value="blindsLeft"<?php rg_selected($instance['animation_type'], 'blindsLeft'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_LEFT'); ?></option>
        <option value="blindsDownLeft"<?php rg_selected($instance['animation_type'], 'blindsDownLeft'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_DOWN_LEFT'); ?></option>
        <option value="blindsDownRight"<?php rg_selected($instance['animation_type'], 'blindsDownRight'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_DOWN_RIGHT'); ?></option>
        <option value="blindsMirrorIn"<?php rg_selected($instance['animation_type'], 'blindsMirrorIn'); ?>><?php rc_e('ROKGALLERY_BLINDS_MIRROR_OUT_TO_IN'); ?></option>
        <option value="blindsMirrorOut"<?php rg_selected($instance['animation_type'], 'blindsMirrorOut'); ?>><?php rc_e('ROKGALLERY_BLINDS_MIRROR_IN_TO_OUT'); ?></option>
        <option value="blindsRandom"<?php rg_selected($instance['animation_type'], 'blindsRandom'); ?>><?php rc_e('ROKGALLERY_BLINDS_RANDOMIZE'); ?></option>
        <option value="boxesRight"<?php rg_selected($instance['animation_type'], 'boxesRight'); ?>><?php rc_e('ROKGALLERY_BOXES_TO_RIGHT'); ?></option>
        <option value="boxesLeft"<?php rg_selected($instance['animation_type'], 'boxesLeft'); ?>><?php rc_e('ROKGALLERY_BOXES_TO_LEFT'); ?></option>
        <option value="boxesOpacityRight"<?php rg_selected($instance['animation_type'], 'boxesOpacityRight'); ?>><?php rc_e('ROKGALLERY_BOXES_OPACITY_TO_RIGHT'); ?></option>
        <option value="boxesOpacityLeft"<?php rg_selected($instance['animation_type'], 'boxesOpacityLeft'); ?>><?php rc_e('ROKGALLERY_BOXES_OPACITY_TO_LEFT'); ?></option>
        <option value="boxesMirror"<?php rg_selected($instance['animation_type'], 'boxesMirror'); ?>><?php rc_e('ROKGALLERY_BOXES_MIRROR'); ?></option>
        <option value="boxesRandom"<?php rg_selected($instance['animation_type'], 'boxesRandom'); ?>><?php rc_e('ROKGALLERY_BOXES_RANDOMIZE'); ?></option>
        <option value="slideDown"<?php rg_selected($instance['animation_type'], 'slideDown'); ?>><?php rc_e('ROKGALLERY_SLIDE_DOWN'); ?></option>
        <option value="slideUp"<?php rg_selected($instance['animation_type'], 'slideUp'); ?>><?php rc_e('ROKGALLERY_SLIDE_UP'); ?></option>
        <option value="slideLeft"<?php rg_selected($instance['animation_type'], 'slideLeft'); ?>><?php rc_e('ROKGALLERY_SLIDE_LEFT'); ?></option>
        <option value="slideRight"<?php rg_selected($instance['animation_type'], 'slideRight'); ?>><?php rc_e('ROKGALLERY_SLIDE_RIGHT'); ?></option>
    </select>
</p>

<p class="slideshow layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_ANIMATION_DURATION'); ?>"
           for="<?php echo rg_get_field_id($instance, 'animation_duration'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_ANIMATION_DURATION'); ?>
    </label>
    <input class="widefat" id="<?php echo rg_get_field_id($instance, 'animation_duration'); ?>"
           name="<?php echo rg_get_field_name($instance, 'animation_duration'); ?>" type="text"
           value="<?php echo $instance['animation_duration']; ?>"/>
</p>

<p class="slideshow layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_AUTOPLAY_ENABLED'); ?>"
           for="<?php echo rg_get_field_id($instance, 'autoplay_enabled'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_AUTOPLAY_ENABLED'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'autoplay_enabled'); ?>"
            name="<?php echo rg_get_field_name($instance, 'autoplay_enabled'); ?>">
        <option value="1"<?php rg_selected($instance['autoplay_enabled'], '1'); ?>><?php rc_e('ROKGALLERY_ENABLED'); ?></option>
        <option value="2"<?php rg_selected($instance['autoplay_enabled'], '2'); ?>><?php rc_e('ROKGALLERY_ENABLED_WITH_PROGRESS'); ?></option>
        <option value="0"<?php rg_selected($instance['autoplay_enabled'], '0'); ?>><?php rc_e('ROKGALLERY_DISABLED'); ?></option>
    </select>
</p>

<p class="slideshow layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_LABEL_AUTOPLAY_DELAY'); ?>"
           for="<?php echo rg_get_field_id($instance, 'autoplay_delay'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_AUTOPLAY_DELAY'); ?>
    </label>
    <input class="widefat" id="<?php echo rg_get_field_id($instance, 'autoplay_delay'); ?>"
           name="<?php echo rg_get_field_name($instance, 'autoplay_delay'); ?>" type="text"
           value="<?php echo $instance['autoplay_delay']; ?>"/>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_ARROWS'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_arrows'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_ARROWS'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_arrows'); ?>"
            name="<?php echo rg_get_field_name($instance, 'showcase_arrows'); ?>">
        <option value="yes"<?php rg_selected($instance['showcase_arrows'], 'yes'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
        <option value="no"<?php rg_selected($instance['showcase_arrows'], 'no'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
        <option value="onhover"<?php rg_selected($instance['showcase_arrows'], 'onhover'); ?>><?php rc_e('ROKGALLERY_HOVER'); ?></option>
    </select>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOWCASE_IMAGEPOSITION'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_image_position'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SHOWCASE_IMAGEPOSITION'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_image_position'); ?>"
            name="<?php echo rg_get_field_name($instance, 'showcase_image_position'); ?>">
        <option value="left"<?php rg_selected($instance['showcase_image_position'], 'left'); ?>><?php rc_e('ROKGALLERY_LEFT'); ?></option>
        <option value="right"<?php rg_selected($instance['showcase_image_position'], 'right'); ?>><?php rc_e('ROKGALLERY_RIGHT'); ?></option>
    </select>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOWCASE_IMGPADDING'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_imgpadding'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SHOWCASE_IMGPADDING'); ?>
    </label>
    <input class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_imgpadding'); ?>"
           name="<?php echo rg_get_field_name($instance, 'showcase_imgpadding'); ?>" type="text"
           value="<?php echo $instance['showcase_imgpadding']; ?>"/>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOWCASE_FIXEDHEIGHT'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_fixedheight'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SHOWCASE_FIXEDHEIGHT'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_fixedheight'); ?>"
            name="<?php echo rg_get_field_name($instance, 'showcase_fixedheight'); ?>">
        <option value="1"<?php rg_selected($instance['showcase_fixedheight'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
        <option value="0"<?php rg_selected($instance['showcase_fixedheight'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
    </select>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOWCASE_ANIMATEDHEIGHT'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_animatedheight'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SHOWCASE_ANIMATEDHEIGHT'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_animatedheight'); ?>"
            name="<?php echo rg_get_field_name($instance, 'showcase_animatedheight'); ?>">
        <option value="1"<?php rg_selected($instance['showcase_animatedheight'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
        <option value="0"<?php rg_selected($instance['showcase_animatedheight'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
    </select>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SLICEANIMATION_TYPE'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_animation_type'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SLICEANIMATION_TYPE'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_animation_type'); ?>"
            name="<?php echo rg_get_field_name($instance, 'showcase_animation_type'); ?>">
        <option value="random"<?php rg_selected($instance['showcase_animation_type'], 'random'); ?>><?php rc_e('ROKGALLERY_RANDOM'); ?></option>
        <option value="crossfade"<?php rg_selected($instance['showcase_animation_type'], 'crossfade'); ?>><?php rc_e('ROKGALLERY_CROSSFADE'); ?></option>
        <option value="blindsRight"<?php rg_selected($instance['showcase_animation_type'], 'blindsRight'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_RIGHT'); ?></option>
        <option value="blindsLeft"<?php rg_selected($instance['showcase_animation_type'], 'blindsLeft'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_LEFT'); ?></option>
        <option value="blindsDownLeft"<?php rg_selected($instance['showcase_animation_type'], 'blindsDownLeft'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_DOWN_LEFT'); ?></option>
        <option value="blindsDownRight"<?php rg_selected($instance['showcase_animation_type'], 'blindsDownRight'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_DOWN_RIGHT'); ?></option>
        <option value="blindsMirrorIn"<?php rg_selected($instance['showcase_animation_type'], 'blindsMirrorIn'); ?>><?php rc_e('ROKGALLERY_BLINDS_MIRROR_OUT_TO_IN'); ?></option>
        <option value="blindsMirrorOut"<?php rg_selected($instance['showcase_animation_type'], 'blindsMirrorOut'); ?>><?php rc_e('ROKGALLERY_BLINDS_MIRROR_IN_TO_OUT'); ?></option>
        <option value="blindsRandom"<?php rg_selected($instance['showcase_animation_type'], 'blindsRandom'); ?>><?php rc_e('ROKGALLERY_BLINDS_RANDOMIZE'); ?></option>
        <option value="boxesRight"<?php rg_selected($instance['showcase_animation_type'], 'boxesRight'); ?>><?php rc_e('ROKGALLERY_BOXES_TO_RIGHT'); ?></option>
        <option value="boxesLeft"<?php rg_selected($instance['showcase_animation_type'], 'boxesLeft'); ?>><?php rc_e('ROKGALLERY_BOXES_TO_LEFT'); ?></option>
        <option value="boxesOpacityRight"<?php rg_selected($instance['showcase_animation_type'], 'boxesOpacityRight'); ?>><?php rc_e('ROKGALLERY_BOXES_OPACITY_TO_RIGHT'); ?></option>
        <option value="boxesOpacityLeft"<?php rg_selected($instance['showcase_animation_type'], 'boxesOpacityLeft'); ?>><?php rc_e('ROKGALLERY_BOXES_OPACITY_TO_LEFT'); ?></option>
        <option value="boxesMirror"<?php rg_selected($instance['showcase_animation_type'], 'boxesMirror'); ?>><?php rc_e('ROKGALLERY_BOXES_MIRROR'); ?></option>
        <option value="boxesRandom"<?php rg_selected($instance['showcase_animation_type'], 'boxesRandom'); ?>><?php rc_e('ROKGALLERY_BOXES_RANDOMIZE'); ?></option>
        <option value="slideDown"<?php rg_selected($instance['showcase_animation_type'], 'slideDown'); ?>><?php rc_e('ROKGALLERY_SLIDE_DOWN'); ?></option>
        <option value="slideUp"<?php rg_selected($instance['showcase_animation_type'], 'slideUp'); ?>><?php rc_e('ROKGALLERY_SLIDE_UP'); ?></option>
        <option value="slideLeft"<?php rg_selected($instance['showcase_animation_type'], 'slideLeft'); ?>><?php rc_e('ROKGALLERY_SLIDE_LEFT'); ?></option>
        <option value="slideRight"<?php rg_selected($instance['showcase_animation_type'], 'slideRight'); ?>><?php rc_e('ROKGALLERY_SLIDE_RIGHT'); ?></option>
    </select>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_CAPTIONSANIMATION_TYPE'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_captionsanimation'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_CAPTIONSANIMATION_TYPE'); ?>
    </label>
    <select class="widefat showcase layout"
            id="<?php echo rg_get_field_id($instance, 'showcase_captionsanimation'); ?>"
            name="<?php echo rg_get_field_name($instance, 'showcase_captionsanimation'); ?>">
        <option value="crossfade"<?php rg_selected($instance['showcase_captionsanimation'], 'crossfade'); ?>><?php rc_e('ROKGALLERY_CROSSFADE'); ?></option>
        <option value="topdown"<?php rg_selected($instance['showcase_captionsanimation'], 'topdown'); ?>><?php rc_e('ROKGALLERY_TOP_DOWN'); ?></option>
        <option value="bottomup"<?php rg_selected($instance['showcase_captionsanimation'], 'bottomup'); ?>><?php rc_e('ROKGALLERY_BOTTOM_UP'); ?></option>
    </select>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_ANIMATION_DURATION'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_animation_duration'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_ANIMATION_DURATION'); ?>
    </label>
    <input class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_animation_duration'); ?>"
           name="<?php echo rg_get_field_name($instance, 'showcase_animation_duration'); ?>" type="text"
           value="<?php echo $instance['showcase_animation_duration']; ?>"/>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOWCASE_AUTOPLAY_ENABLED'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_autoplay_enabled'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_AUTOPLAY_ENABLED'); ?>
        <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_autoplay_enabled'); ?>"
                name="<?php echo rg_get_field_name($instance, 'showcase_autoplay_enabled'); ?>">
            <option value="1"<?php rg_selected($instance['showcase_autoplay_enabled'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
            <option value="0"<?php rg_selected($instance['showcase_autoplay_enabled'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
        </select>
</p>

<p class="showcase layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_AUTOPLAY_DURATION'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_autoplay_delay'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_AUTOPLAY_DURATION'); ?>
    </label>
    <input class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_autoplay_delay'); ?>"
           name="<?php echo rg_get_field_name($instance, 'showcase_autoplay_delay'); ?>" type="text"
           value="<?php echo $instance['showcase_autoplay_delay']; ?>"/>
</p>

<p class="showcase_responsive layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_ARROWS'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_responsive_arrows'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_ARROWS'); ?>
        <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_responsive_arrows'); ?>"
                name="<?php echo rg_get_field_name($instance, 'showcase_responsive_arrows'); ?>">
            <option value="yes"<?php rg_selected($instance['showcase_responsive_arrows'], 'yes'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
            <option value="no"<?php rg_selected($instance['showcase_responsive_arrows'], 'no'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
            <option value="onhover"<?php rg_selected($instance['showcase_responsive_arrows'], 'onhover'); ?>><?php rc_e('ROKGALLERY_HOVER'); ?></option>
        </select>
</p>

<p class="showcase_responsive layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOWCASE_IMAGEPOSITION'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_responsive_image_position'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SHOWCASE_IMAGEPOSITION'); ?>
        <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_responsive_image_position'); ?>"
                name="<?php echo rg_get_field_name($instance, 'showcase_responsive_image_position'); ?>">
            <option value="left"<?php rg_selected($instance['showcase_responsive_image_position'], 'yes'); ?>><?php rc_e('ROKGALLERY_LEFT'); ?></option>
            <option value="right"<?php rg_selected($instance['showcase_responsive_image_position'], 'no'); ?>><?php rc_e('ROKGALLERY_RIGHT'); ?></option>
        </select>
</p>

<p class="showcase_responsive layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SHOWCASE_IMGPADDING'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_responsive_imgpadding'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SHOWCASE_IMGPADDING'); ?>
    </label>
    <input class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_responsive_imgpadding'); ?>"
           name="<?php echo rg_get_field_name($instance, 'showcase_responsive_imgpadding'); ?>" type="text"
           value="<?php echo $instance['showcase_responsive_imgpadding']; ?>"/>
</p>

<p class="showcase_responsive layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_SLICEANIMATION_TYPE'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_responsive_animation_type'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_SLICEANIMATION_TYPE'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_responsive_animation_type'); ?>"
            name="<?php echo rg_get_field_name($instance, 'showcase_responsive_animation_type'); ?>">
        <option value="random"<?php rg_selected($instance['showcase_responsive_animation_type'], 'random'); ?>><?php rc_e('ROKGALLERY_RANDOM'); ?></option>
        <option value="crossfade"<?php rg_selected($instance['showcase_responsive_animation_type'], 'crossfade'); ?>><?php rc_e('ROKGALLERY_CROSSFADE'); ?></option>
        <option value="blindsRight"<?php rg_selected($instance['showcase_responsive_animation_type'], 'blindsRight'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_RIGHT'); ?></option>
        <option value="blindsLeft"<?php rg_selected($instance['showcase_responsive_animation_type'], 'blindsLeft'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_LEFT'); ?></option>
        <option value="blindsDownLeft"<?php rg_selected($instance['showcase_responsive_animation_type'], 'blindsDownLeft'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_DOWN_LEFT'); ?></option>
        <option value="blindsDownRight"<?php rg_selected($instance['showcase_responsive_animation_type'], 'blindsDownRight'); ?>><?php rc_e('ROKGALLERY_BLINDS_TO_DOWN_RIGHT'); ?></option>
        <option value="blindsMirrorIn"<?php rg_selected($instance['showcase_responsive_animation_type'], 'blindsMirrorIn'); ?>><?php rc_e('ROKGALLERY_BLINDS_MIRROR_OUT_TO_IN'); ?></option>
        <option value="blindsMirrorOut"<?php rg_selected($instance['showcase_responsive_animation_type'], 'blindsMirrorOut'); ?>><?php rc_e('ROKGALLERY_BLINDS_MIRROR_IN_TO_OUT'); ?></option>
        <option value="blindsRandom"<?php rg_selected($instance['showcase_responsive_animation_type'], 'blindsRandom'); ?>><?php rc_e('ROKGALLERY_BLINDS_RANDOMIZE'); ?></option>
        <option value="boxesRight"<?php rg_selected($instance['showcase_responsive_animation_type'], 'boxesRight'); ?>><?php rc_e('ROKGALLERY_BOXES_TO_RIGHT'); ?></option>
        <option value="boxesLeft"<?php rg_selected($instance['showcase_responsive_animation_type'], 'boxesLeft'); ?>><?php rc_e('ROKGALLERY_BOXES_TO_LEFT'); ?></option>
        <option value="boxesOpacityRight"<?php rg_selected($instance['showcase_responsive_animation_type'], 'boxesOpacityRight'); ?>><?php rc_e('ROKGALLERY_BOXES_OPACITY_TO_RIGHT'); ?></option>
        <option value="boxesOpacityLeft"<?php rg_selected($instance['showcase_responsive_animation_type'], 'boxesOpacityLeft'); ?>><?php rc_e('ROKGALLERY_BOXES_OPACITY_TO_LEFT'); ?></option>
        <option value="boxesMirror"<?php rg_selected($instance['showcase_responsive_animation_type'], 'boxesMirror'); ?>><?php rc_e('ROKGALLERY_BOXES_MIRROR'); ?></option>
        <option value="boxesRandom"<?php rg_selected($instance['showcase_responsive_animation_type'], 'boxesRandom'); ?>><?php rc_e('ROKGALLERY_BOXES_RANDOMIZE'); ?></option>
        <option value="slideDown"<?php rg_selected($instance['showcase_responsive_animation_type'], 'slideDown'); ?>><?php rc_e('ROKGALLERY_SLIDE_DOWN'); ?></option>
        <option value="slideUp"<?php rg_selected($instance['showcase_responsive_animation_type'], 'slideUp'); ?>><?php rc_e('ROKGALLERY_SLIDE_UP'); ?></option>
        <option value="slideLeft"<?php rg_selected($instance['showcase_responsive_animation_type'], 'slideLeft'); ?>><?php rc_e('ROKGALLERY_SLIDE_LEFT'); ?></option>
        <option value="slideRight"<?php rg_selected($instance['showcase_responsive_animation_type'], 'slideRight'); ?>><?php rc_e('ROKGALLERY_SLIDE_RIGHT'); ?></option>
    </select>
</p>

<p class="showcase_responsive layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_CAPTIONSANIMATION_TYPE'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_responsive_captionsanimation'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_CAPTIONSANIMATION_TYPE'); ?>
    </label>
    <select class="widefat"
            id="<?php echo rg_get_field_id($instance, 'showcase_responsive_captionsanimation'); ?>"
            name="<?php echo rg_get_field_name($instance, 'showcase_responsive_captionsanimation'); ?>">
        <option value="crossfade"<?php rg_selected($instance['showcase_responsive_captionsanimation'], 'crossfade'); ?>><?php rc_e('ROKGALLERY_CROSSFADE'); ?></option>
        <option value="topdown"<?php rg_selected($instance['showcase_responsive_captionsanimation'], 'topdown'); ?>><?php rc_e('ROKGALLERY_TOP_DOWN'); ?></option>
        <option value="bottomup"<?php rg_selected($instance['showcase_responsive_captionsanimation'], 'bottomup'); ?>><?php rc_e('ROKGALLERY_BOTTOM_UP'); ?></option>
    </select>
</p>

<p class="showcase_responsive layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_ANIMATION_DURATION'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_responsive_animation_duration'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_ANIMATION_DURATION'); ?>
    </label>
    <input class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_responsive_animation_duration'); ?>"
           name="<?php echo rg_get_field_name($instance, 'showcase_responsive_animation_duration'); ?>" type="text"
           value="<?php echo $instance['showcase_responsive_animation_duration']; ?>"/>
</p>

<p class="showcase_responsive layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_DESC_AUTOPLAY_ENABLED'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_responsive_autoplay_enabled'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_AUTOPLAY_ENABLED'); ?>
    </label>
    <select class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_responsive_autoplay_enabled'); ?>"
            name="<?php echo rg_get_field_name($instance, 'showcase_responsive_autoplay_enabled'); ?>">
        <option value="1"<?php rg_selected($instance['showcase_responsive_autoplay_enabled'], '1'); ?>><?php rc_e('ROKGALLERY_ENABLED'); ?></option>
        <option value="2"<?php rg_selected($instance['showcase_responsive_autoplay_enabled'], '2'); ?>><?php rc_e('ROKGALLERY_ENABLED_WITH_PROGRESS'); ?></option>
        <option value="0"<?php rg_selected($instance['showcase_responsive_autoplay_enabled'], '0'); ?>><?php rc_e('ROKGALLERY_DISABLED'); ?></option>
    </select>
</p>

<p class="showcase_responsive layout">
    <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_LABEL_AUTOPLAY_DELAY'); ?>"
           for="<?php echo rg_get_field_id($instance, 'showcase_responsive_autoplay_delay'); ?>">
        <?php rc_e('ROKGALLERY_LABEL_AUTOPLAY_DELAY'); ?>
    </label>
    <input class="widefat" id="<?php echo rg_get_field_id($instance, 'showcase_responsive_autoplay_delay'); ?>"
           name="<?php echo rg_get_field_name($instance, 'showcase_responsive_autoplay_delay'); ?>" type="text"
           value="<?php echo $instance['showcase_responsive_autoplay_delay']; ?>"/>
</p>
</div>
<div style="clear:both;"></div>
<!-- End RokGallery Widget Admin -->
