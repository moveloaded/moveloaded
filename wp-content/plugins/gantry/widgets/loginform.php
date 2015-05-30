<?php
/**
 * @version   4.1.4 March 11, 2015
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

	static function init() {
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
						<label for="modlgn-username"><?php _e('User Name', 'gantry'); ?></label>
						<input id="modlgn-username" type="text" name="log" class="inputbox" alt="username" size="18" value="" />
					</p>
					<p id="form-login-password">
						<label for="modlgn-passwd"><?php _e('Password', 'gantry'); ?></label>
						<input id="modlgn-passwd" type="password" name="pwd" class="inputbox" size="18" alt="password" value="" />
					</p>
					<p id="form-login-remember">
						<label for="modlgn-remember"><?php _e('Remember Me', 'gantry'); ?></label>
						<input id="modlgn-remember" type="checkbox" name="rememberme" class="inputbox" />
					</p>
					<input type="submit" value="<?php _e('Log in', 'gantry'); ?>" class="button" name="submit" />
				</fieldset>				
				<ul>
					<li>
						<a href="<?php echo wp_lostpassword_url(); ?>"><?php _e('Forgot your password?', 'gantry'); ?></a>
					</li>
					<?php if(get_option('users_can_register')) : ?>
					<li>
						<a href="<?php echo site_url('/wp-login.php?action=register&redirect_to=' . get_permalink()); ?>"><?php _e('Register', 'gantry'); ?></a>
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
					<input type="submit" name="Submit" class="button" value="<?php _e('Log out', 'gantry'); ?>" />
				</div>
			</form>
		
		<?php endif; ?>
    	
	    <?php 
	    
	    echo ob_get_clean();
	
	}
}