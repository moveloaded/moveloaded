<?php
 /**
  * @version   $Id: includes.php 10867 2013-05-30 04:04:46Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

RokCommon_Header::addStyle(RokCommon_Composite::get($that->context)->getUrl('detail.css'));
RokCommon_Header::addStyle(RokCommon_Composite::get($that->style_context)->getUrl('style.css'));
RokCommon_Header::addInlineScript(RokCommon_Composite::get('rokgallery')->load('js-settings.php', array('that'=>$that)));

