<?php
/**
 * @version   $Id: default.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
RokCommon_Composite::get($view->context)->load('includes.php', array('that' => $view));
echo RokCommon_Composite::get($view->context)->load('default.php', array('that' => $view));