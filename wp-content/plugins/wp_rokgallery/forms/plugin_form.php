<?php
/**
 * @version   $Id: plugin_form.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

//load custom field classes
?>
<script type="text/javascript">
    window.addEvent('domready', function () {
        new Tips(".rok-tips",{title:"data-tips"});
    });
</script>

<!-- Begin RokGallery Admin Form -->
<?php if (RokGallery_Request::getBool('settings-updated') == 'true') { ?>
	<div id="message" class="updated fade"><p><?php rc_e('ROKGALLERY_SETTINGS_SAVED'); ?></p></div>
<?php } ?>

<div class="wrap">
    <div class="page-header">
        <div class="icon32 icon32-posts-rokgallery" id="icon-rokgallery"><br /></div>
        <h2>RokGallery Settings</h2>

        <div class="back-button">
            <a href="<?php echo $instance['admin_link'];?>">
                <span>Back to Admin</span>
            </a>
        </div>
    </div>
    <div style="clear:both;"></div>
    <div class="rokgallery-form">
        <form method="post" action="options.php">
            <?php settings_fields('rokgallery_settings_group'); ?>

            <table cellspacing="0" class="widefat fixed" style="margin: 0 auto; width: 50%;">
                <thead>
                <tr>
                    <th colspan="2">
                        <input type="submit" class="button action" value="<?php rc_e('ROKGALLERY_SAVE_CHANGES'); ?>"
                               style="padding: 3px; float: right;"/>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr class="iedit">
                    <td>
                        <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_ALLOW_DUP_FILES'); ?>"
                               for="<?php echo rg_get_option_id('allow_duplicate_files'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_ALLOW_DUP_FILES'); ?>
                        </label>
                    </td>
                    <td>
                        <select class="" id="<?php echo rg_get_option_id('allow_duplicate_files'); ?>"
                                name="<?php echo rg_get_option_name('allow_duplicate_files'); ?>">
                            <option value="1"<?php rg_selected($instance['allow_duplicate_files'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
                            <option value="0"<?php rg_selected($instance['allow_duplicate_files'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr class="alternate iedit">
                    <td>
                        <label class="rok-tips"
                               data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_AUTOPUBLISH_SLICES'); ?>"
                               for="<?php echo rg_get_option_id('publish_slices_on_file_publish'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_AUTOPUBLISH_SLICES'); ?>
                        </label>
                    </td>
                    <td>
                        <select class="" id="<?php echo rg_get_option_id('publish_slices_on_file_publish'); ?>"
                                name="<?php echo rg_get_option_name('publish_slices_on_file_publish'); ?>">
                            <option value="1"<?php rg_selected($instance['publish_slices_on_file_publish'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
                            <option value="0"<?php rg_selected($instance['publish_slices_on_file_publish'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr class="iedit">
                    <td>
                        <label class="rok-tips"
                               data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_GALLERY_AUTOPUBLISH'); ?>"
                               for="<?php echo rg_get_option_id('gallery_remove_slice'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_GALLERY_REMOVE_SLICES'); ?>
                        </label>
                    </td>
                    <td>
                        <select class="" id="<?php echo rg_get_option_id('gallery_remove_slice'); ?>"
                                name="<?php echo rg_get_option_name('gallery_remove_slice'); ?>">
                            <option value="1"<?php rg_selected($instance['gallery_remove_slice'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
                            <option value="0"<?php rg_selected($instance['gallery_remove_slice'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr class="alternate iedit">
                    <td>
                        <label class="rok-tips"
                               data-tips="<?php rc_e('ROKGALLERY_SETTINGS_LABEL_GALLERY_AUTOPUBLISH'); ?>"
                               for="<?php echo rg_get_option_id('gallery_autopublish'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_GALLERY_AUTOPUBLISH'); ?>
                        </label>
                    </td>
                    <td>
                        <select class="" id="<?php echo rg_get_option_id('gallery_autopublish'); ?>"
                                name="<?php echo rg_get_option_name('gallery_autopublish'); ?>">
                            <option value="1"<?php rg_selected($instance['gallery_autopublish'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
                            <option value="0"<?php rg_selected($instance['gallery_autopublish'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr class="iedit">
                    <td>
                        <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_THUMB_XSIZE'); ?>"
                               for="<?php echo rg_get_option_id('default_thumb_xsize'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_THUMB_XSIZE'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="widefat" id="<?php echo rg_get_option_id('default_thumb_xsize'); ?>"
                               name="<?php echo rg_get_option_name('default_thumb_xsize'); ?>" type="text"
                               value="<?php echo $instance['default_thumb_xsize']; ?>"/>
                    </td>
                </tr>

                <tr class="alternate iedit">
                    <td>
                        <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_THUMB_YSIZE'); ?>"
                               for="<?php echo rg_get_option_id('default_thumb_ysize'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_THUMB_YSIZE'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="widefat" id="<?php echo rg_get_option_id('default_thumb_ysize'); ?>"
                               name="<?php echo rg_get_option_name('default_thumb_ysize'); ?>" type="text"
                               value="<?php echo $instance['default_thumb_ysize']; ?>"/>
                    </td>
                </tr>

                <tr class="iedit">
                    <td>
                        <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_THUMB_KEEP_ASPECT'); ?>"
                               for="<?php echo rg_get_option_id('default_thumb_keep_aspect'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_THUMB_KEEP_ASPECT'); ?>
                        </label>
                    </td>
                    <td>
                        <select class="" id="<?php echo rg_get_option_id('default_thumb_keep_aspect'); ?>"
                                name="<?php echo rg_get_option_name('default_thumb_keep_aspect'); ?>">
                            <option value="1"<?php rg_selected($instance['default_thumb_keep_aspect'], '1'); ?>><?php rc_e('ROKGALLERY_YES'); ?></option>
                            <option value="0"<?php rg_selected($instance['default_thumb_keep_aspect'], '0'); ?>><?php rc_e('ROKGALLERY_NO'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr class="iedit">
                    <td>
                        <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_THUMB_BACKGROUND'); ?>"
                               for="<?php echo rg_get_option_id('default_thumb_background'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_THUMB_BACKGROUND'); ?>
                        </label>
                    </td>
                    <td>
                        <?php echo RokGallery_Forms_Fields_ColorChooser::getInput(rg_get_option_name('default_thumb_background'), rg_get_option_id('default_thumb_background'), $instance['default_thumb_background']);?>
                    </td>
                </tr>

                <tr class="alternate iedit">
                    <td>
                        <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_JPEG_QUALITY'); ?>"
                               for="<?php echo rg_get_option_id('jpeg_quality'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_JPEG_QUALITY'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="widefat" id="<?php echo rg_get_option_id('jpeg_quality'); ?>"
                               name="<?php echo rg_get_option_name('jpeg_quality'); ?>" type="text"
                               value="<?php echo $instance['jpeg_quality']; ?>"/>
                    </td>
                </tr>

                <tr class="iedit">
                    <td>
                        <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_PNG_COMPRESSION'); ?>"
                               for="<?php echo rg_get_option_id('default_thumb_ysize'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_PNG_COMPRESSION'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="widefat" id="<?php echo rg_get_option_id('png_compression'); ?>"
                               name="<?php echo rg_get_option_name('png_compression'); ?>" type="text"
                               value="<?php echo $instance['png_compression']; ?>"/>
                    </td>
                </tr>

                <tr class="alternate iedit">
                    <td>
                        <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_LOVE_TEXT'); ?>"
                               for="<?php echo rg_get_option_id('love_text'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_LOVE_TEXT'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="widefat" id="<?php echo rg_get_option_id('love_text'); ?>"
                               name="<?php echo rg_get_option_name('love_text'); ?>" type="text"
                               value="<?php echo $instance['love_text']; ?>"/>
                    </td>
                </tr>

                <tr class="iedit">
                    <td>
                        <label class="rok-tips" data-tips="<?php rc_e('ROKGALLERY_SETTINGS_DESC_UNLOVE_TEXT'); ?>"
                               for="<?php echo rg_get_option_id('unlove_text'); ?>">
                            <?php rc_e('ROKGALLERY_SETTINGS_LABEL_UNLOVE_TEXT'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="widefat" id="<?php echo rg_get_option_id('unlove_text'); ?>"
                               name="<?php echo rg_get_option_name('unlove_text'); ?>" type="text"
                               value="<?php echo $instance['unlove_text']; ?>"/>
                    </td>
                </tr>
                </tbody>

                <tfoot class="widget-top">
                <tr>
                    <th colspan="2">
                        <input type="submit" class="button action" value="<?php rc_e('ROKGALLERY_SAVE_CHANGES'); ?>"
                               style="padding: 3px; float: right;"/>
                    </th>
                </tr>
                </tfoot>
            </table>
        </form>
    </div>

</div>

</div>
<div style="clear:both;"></div>
<!-- End RokGallery Admin Form -->
