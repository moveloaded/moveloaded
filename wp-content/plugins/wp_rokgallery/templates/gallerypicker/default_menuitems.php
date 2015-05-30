<?php
/**
  * @version   $Id: default_menuitems.php 10867 2013-05-30 04:04:46Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

?>

<table id="gallerypicker-menulist" class="adminlist">
    <thead>
        <tr>
            <th class="title">Menu Item</th>
            <th width="15%">Status</th>
            <th width="1%" class="nowrap">Id</th>
        </tr>
    </thead>
    <tbody>
<?php
$menuitems = $that->menuitems;
$count = 0;
foreach($menuitems as $menu_item):
?>
        <tr class="row<?php echo ($count % 2); ?>">
            <td class="menuitem">
                <a data-opentag="<a href='<?php echo $menu_item->url;?>'>" data-closetag="</a>" data-display="<?php echo $menu_item->title;?>" class="menu-item" href="<?php echo $menu_item->link;?>"><?php echo $menu_item->title;?></a>
            </td>
            <td class="menuitemid center">
                <span class="<?php echo $menu_item->post_status;?>"><?php echo $menu_item->post_status;?></span>
            </td>
            <td class="menuitemid center">
                <span><?php echo $menu_item->ID; ?></span>
            </td>
        </tr>
<?php $count++; endforeach; ?>
    </tbody>
</table>
