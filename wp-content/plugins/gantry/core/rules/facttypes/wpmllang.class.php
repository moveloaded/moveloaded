<?php
/**
 * @version   $Id: templatepage.class.php 59361 2013-03-13 23:10:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

gantry_import('core.rules.gantryoverridefact');

add_filter('gantry_check_wpml_lang_function', 'GantryFactWPMLLang::getPageCheckConditionalFunction');

class GantryFactWPMLLang extends GantryOverrideFact
{
	function matchesCallWPMLLang($query)
	{
		$ret   = false;
		$check = apply_filters('gantry_check_wpml_lang_function', $this->type);
		if (isset($query->$check)) {
			$ret = $query->$check;
		}
		return $ret;
	}

	public static function getPageCheckConditionalFunction($type)
	{
		return "is_wpml_lang_" . $type;
	}
}