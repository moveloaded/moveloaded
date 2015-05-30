<?php
 /**
 * @version   $Id: js-settings.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


echo "
	if (typeof RokGallery == 'undefined') var RokGallery = {};
	RokGallery.url = '" . $that->base_ajax_url . "';
	RokGallery.ajaxVars = {action: 'model_action', model: 'model'};
";
