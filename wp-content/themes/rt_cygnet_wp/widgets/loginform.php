<?php
/**
 * @version   1.0 May 15, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
 
defined('GANTRY_VERSION') or die();

gantry_import('core.gantrywidget');

add_action('widgets_init', array("GantryWidgetLoginForm","init"));

class GantryWidgetLoginForm extends GantryWidget {
    var $short_name = 'loginform';
    var $wp_name = 'gantry_loginform';
    var $long_name = 'Gantry Login Form';
    var $description = 'Gantry Login Form Widget';
    var $css_classname = 'widget_gantry_loginform';
    var $width = 200;
    var $height = 400;

    function init() {
        register_widget("GantryWidgetLoginForm");
    }
    
    function render_title($args, $instance) {
    	global $gantry;
    	if($instance['title'] != '') :
    		echo apply_filters( 'widget_title', $instance['title'], $instance );
    	endif;
    }

    function render($args, $instance){
        global $gantry, $current_user;
	    ob_start();
	    ?>
    	
    	<?php if(!is_user_logged_in()) : ?>
		
			<form action="<?php echo wp_login_url($_SERVER['REQUEST_URI']); ?>" method="post" id="login-form">
				<?php if ($instance['pretext'] != ''): ?>
				<div class="pretext">
					<p><?php echo $instance['pretext']; ?></p>
				</div>
				<?php endif; ?>
				<fieldset class="userdata">
					<p id="form-login-username">
						<input id="modlgn-username" type="text" name="log" class="inputbox" alt="username" size="18" value="<?php _e( 'User Name', 'rt_cygnet_wp_lang' ); ?>" onfocus="if (this.value=='<?php _e( 'User Name', 'rt_cygnet_wp_lang' ); ?>') this.value=''" onblur="if(this.value=='') { this.value='<?php _e( 'User Name', 'rt_cygnet_wp_lang' ); ?>'; return false; }" />
					</p>
					<p id="form-login-password">
						<input id="modlgn-passwd" type="password" name="pwd" class="inputbox" size="18" alt="password" value="<?php _e( 'Password', 'rt_cygnet_wp_lang' ); ?>" onfocus="if (this.value=='<?php _e( 'Password', 'rt_cygnet_wp_lang' ); ?>') this.value=''" onblur="if(this.value=='') { this.value='<?php _e( 'Password', 'rt_cygnet_wp_lang' ); ?>'; return false; }" />
					</p>
					<p id="form-login-remember">
						<label for="modlgn-remember"><?php _e( 'Remember Me', 'rt_cygnet_wp_lang' ); ?></label>
						<input id="modlgn-remember" type="checkbox" name="rememberme" class="inputbox" />
					</p>
					<input type="submit" value="<?php _e( 'Log in', 'rt_cygnet_wp_lang' ); ?>" class="button" name="submit" />
				</fieldset>				
				<ul>
					<li>
						<a href="<?php echo wp_lostpassword_url(); ?>"><?php _e( 'Forgot your password?', 'rt_cygnet_wp_lang' ); ?></a>
					</li>
					<?php if(get_option('users_can_register')) : ?>
					<li>
						<a href="<?php echo site_url('/wp-login.php?action=register&redirect_to=' . get_permalink()); ?>"><?php _e( 'Register', 'rt_cygnet_wp_lang' ); ?></a>
					</li>
					<?php endif; ?>
				</ul>
				<?php if ($instance['posttext'] != ''): ?>
				<div class="posttext">
					<p><?php echo $instance['posttext']; ?></p>
				</div>
				<?php endif; ?>				
			</form>
			
		<?php else : ?>
		
			<form action="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>" method="post" id="login-form">
				<div class="login-greeting">
					<p><?php echo $instance['user_greeting']; ?> <?php echo $current_user->display_name; ?></p>
				</div>
				<div class="logout-button">
					<input type="submit" name="Submit" class="button" value="<?php _e( 'Log out', 'rt_cygnet_wp_lang' ); ?>" />
				</div>
			</form>
		
		<?php endif; ?>
    	
	    <?php 
	    
	    echo ob_get_clean();
	
	}
}