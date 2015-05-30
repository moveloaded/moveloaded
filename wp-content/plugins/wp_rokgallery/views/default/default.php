<?php
 /**
  * @version   $Id: default.php 10867 2013-05-30 04:04:46Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

RokCommon_Composite::get('rokgallery.default')->loadAll('includes.php', array('that' => $view));
?>

<div class="wrap">
    <div class="page-header">
        <div class="icon32 icon32-posts-rokgallery" id="icon-rokgallery"><br /></div>
        <h2>RokGallery Administration</h2><br/>
        <?php echo RokCommon_Composite::get('rg_views.default')->load('toolbar.php', array('buttons' => $view->toolbar));?>
    </div>
    <div style="clear:both;"></div>
    <div class="page-body">
        <?php echo RokCommon_Composite::get('rokgallery.default')->load('default.php', array('that' => $view));?>
    </div>
</div>
<div style="clear:both;"></div>