<?php
/**
 * Created by JetBrains PhpStorm.
 * User: steph
 * Date: 12/3/11
 * Time: 2:58 AM
 * To change this template use File | Settings | File Templates.
 */

?>
<div class="toolbar-wrapper">
    <ul class="toolbar">
        <?php foreach ($buttons as $key => $button):
        ?>
        <li id="<?php echo $button['id'];?>" class="toolbar-button<?php if ($button['extra_class']) {
            echo ' ' . $button['extra_class'];
        }?>">
            <a href="<?php echo $button['link'];?>">
                <span class="<?php echo $button['class'];?>" title="<?php echo $button['title'];?>"></span>
                <?php echo $button['title'];?>
            </a>
        </li>
        <?php endforeach;?>
    </ul>
</div>
<div style="clear:both;"></div>