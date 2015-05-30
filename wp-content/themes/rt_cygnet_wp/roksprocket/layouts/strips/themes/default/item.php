<?php
/**
* @version   $Id: item.php 26103 2015-01-27 14:19:31Z james $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

?>
<li data-strips-item>
	<div class="sprocket-strips-item" data-strips-content>
		<?php if ($item->getPrimaryImage()) :?>
		<div class="sprocket-strips-image-container">
			<?php if ($item->getPrimaryLink()) : ?>
			<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>">
				<span class="sprocket-strips-image-overlay">
			<?php endif; ?>			
			<img src="<?php echo $item->getPrimaryImage()->getSource(); ?>" alt="" />
			<?php if ($item->getPrimaryLink()) : ?>
				</span>
			</a>	
			<?php endif; ?>		
		</div>
		<?php endif; ?>
		<div class="sprocket-strips-content">
			<?php if ($item->getTitle()) : ?>
			<h4 class="sprocket-strips-title" data-strips-toggler>
				<?php if ($item->getPrimaryLink()) : ?><a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>"><?php endif; ?>
					<?php echo $item->getTitle();?>
				<?php if ($item->getPrimaryLink()) : ?></a><?php endif; ?>
			</h4>
			<?php endif; ?>
			<?php if ($item->getText()) :?>
				<span class="sprocket-strips-text">
					<?php echo $item->getText(); ?>
				</span>
			<?php endif; ?>
			<?php if ($item->getPrimaryLink()) : ?>
			<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="readon"><span><?php rc_e('READ_MORE'); ?></span></a>
			<?php endif; ?>
		</div>
	</div>
</li>
