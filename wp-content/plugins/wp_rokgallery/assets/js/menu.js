/**
 * @version $Id: menu.js 10867 2013-05-30 04:04:46Z btowles $
 * @copyright Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/**
 * JMenu javascript behavior
 *
 * @package Joomla
 * @since 1.5
 * @version 1.0
 * @notes MooTools 1.3 version by Djamil Legato
 */

var JMenu = new Class({
	initialize: function(el) {
		var elements = document.getElements('ul#menu li'), nested, width, children;
			
		elements.each(function(element, i){
			element.addEvents({
				'mouseenter': function(){ this.addClass('hover'); },
				'mouseleave': function(){ this.removeClass('hover'); }
			});
			
			width = 0;
			
			nested = element.getElement('ul');
			if (!nested) return;
			
			children = nested.getChildren().filter(function(nest){
				return nest.get('tag') == 'li';
			});
			
			children.each(function(child, i){
				var childWidth = child.offsetWidth;
				width = (width >= childWidth) ? width : childWidth;
			});
			
			children.setStyle('width', width);
			nested.setStyle('width', width);
		});
	}
});
