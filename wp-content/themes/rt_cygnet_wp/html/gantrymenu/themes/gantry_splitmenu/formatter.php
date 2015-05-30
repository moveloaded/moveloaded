<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
 
class GantrySplitMenuFormatter extends AbstractRokMenuFormatter {
	function format_subnode( &$node ) {
		
		if ($node->getId() == $this->current_node) $node->addListItemClass('last');
		
	}
}