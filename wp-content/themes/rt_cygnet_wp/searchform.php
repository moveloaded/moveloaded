<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( 'ABSPATH' ) or die( 'Restricted access' );

global $gantry;
?>

<form role="search" method="get" id="searchform" class="form-inline" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="search rt-suspend">
		<input type="text" class="inputbox search-query" name="s" id="mod-search-searchword" onfocus="if (this.value=='<?php esc_attr_e( 'Search...' ); ?>') this.value='';" onblur="if (this.value=='') this.value='<?php esc_attr_e( 'Search...' ); ?>';" value="<?php echo wp_kses( get_search_query(), null ); ?>" />
	</div>
</form>