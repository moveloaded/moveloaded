<?php
 /**
 * @version   $Id: includes.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

$browser = new RokCommon_Browser();
if ($browser->getShortName() == 'ie8') {
    RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('internet-explorer-8.css'));
}
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('buttons.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('style.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('blocks.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('file-edit.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('file-settings.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('filters.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('galleries.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('jobs.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('master.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('popup.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('tags.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.styles')->getURL('uploader.css'));

RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Common.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('RokGallery.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('RokGallery.Filters.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('RokGallery.Blocks.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('RokGallery.FileSettings.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('RokGallery.Edit.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('MainPage.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Tags.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Tags.Slice.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Tags.Ajax.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Scrollbar.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Popup.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Progress.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Job.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('JobsManager.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('MassTags.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('GalleriesManager.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Swiff.Uploader.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Uploader.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Rubberband.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Marquee.js'));
RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.application')->getURL('Marquee.Crop.js'));