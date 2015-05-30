<?php
/**
 * @version   $Id: rokgallery.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 *
 */
class RokGallery_Widgets_RokGallery extends WP_Widget
{
    /**
     * @var string
     */
    var $short_name = 'rokgallery_options';
    /**
     * @var string
     */
    var $long_name = 'RokGallery';
    /**
     * @var string
     */
    var $description = 'RocketTheme RokGallery Widget';
    /**
     * @var string
     */
    var $css_classname = 'rokgallery_options';
    /**
     * @var int
     */
    var $width = 200;
    /**
     * @var int
     */
    var $height = 400;

    /**
     *
     */
    function __construct()
    {
        if (empty($this->short_name) || empty($this->long_name)) {
            die("A widget must have a valid type and classname defined");
        }
        $widget_options = array('classname' => $this->css_classname, 'description' => __($this->description));
        $control_options = array('width' => $this->width, 'height' => $this->height);
        parent::__construct($this->short_name, $this->long_name, $widget_options, $control_options);
    }

    /**
     * @return array
     */
    public static function get_defaults()
    {
        $defaults = array(
            'gallery_id' => 0,
            'link' => 0,
            'default_menuitem' => 0,
            'show_title' => 0,
            'caption' => 0,
            'sort_by' => 'gallery_ordering',
            'sort_direction' => 'ASC',
            'limit_count' => 10,
            'style' => 'light',
            'layout' => 'grid',
            'columns' => 1,
            'arrows' => 'onhover',
            'navigation' => 'thumbnails',
            'animation_type' => 'random',
            'animation_duration' => 500,
            'autoplay_enabled' => 0,
            'autoplay_delay' => 7,
            'showcase_arrows' => 'onhover',
            'showcase_image_position' => 'left',
            'showcase_imgpadding' => 0,
            'showcase_fixedheight' => 0,
            'showcase_animatedheight' => 1,
            'showcase_animation_type' => 'random',
            'showcase_captionsanimation' => 'crossfade',
            'showcase_animation_duration' => 500,
            'showcase_autoplay_enabled' => 0,
            'showcase_autoplay_delay' => 7,
            'showcase_responsive_arrows' => 'onhover',
            'showcase_responsive_image_position' => 'left',
            'showcase_responsive_imgpadding' => 0,
            'showcase_responsive_fixedheight' => 0,
            'showcase_responsive_animatedheight' => 1,
            'showcase_responsive_animation_type' => 'random',
            'showcase_responsive_captionsanimation' => 'crossfade',
            'showcase_responsive_animation_duration' => 500,
            'showcase_responsive_autoplay_enabled' => 0,
            'showcase_responsive_autoplay_delay' => 7
        );
        add_option('widget_rokgallery_options', $defaults);
        return $defaults;
    }

    /**
     * intilize rokgallery widget
     */
    public static function init()
    {
        if (defined('ROKGALLERY_ERROR_MISSING_LIBS') && ROKGALLERY_ERROR_MISSING_LIBS==true) return;
            register_widget("RokGallery_Widgets_RokGallery");

        if (!is_admin()) {

            //we get the active layout and style
            $views = self::get_active_layouts();
            foreach ($views as $view) {
                $layout_context = 'rokgallery.widget.' . $view['layout'];
                $style_context = $layout_context . '.' . $view['style'];

                RokCommon_Header::addStyle(RokCommon_Composite::get($layout_context)->getURL($view['layout'] . '.css'));
                RokCommon_Header::addStyle(RokCommon_Composite::get($style_context)->getURL('style.css'));
                RokCommon_Header::addScript(RokCommon_Composite::get($layout_context)->getURL($view['layout'] . '.js'));
            }
        } else {
            //add thickbox
            wp_enqueue_script('jquery');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('quicktags');
            add_thickbox();
        }
    }

    /**
     * initialize individual widget instances
     */
    public static function rokgallery_init()
    {
        if (defined('ROKGALLERY_ERROR_MISSING_LIBS') && ROKGALLERY_ERROR_MISSING_LIBS==true) return;

        $option = get_option('widget_rokgallery_options');
        $sidebar_widgets = wp_get_sidebars_widgets();

        foreach ($sidebar_widgets as $sidebar => $sidebar_widgets) {
            if ($sidebar != 'wp_inactive_widgets') {
                if($sidebar_widgets && is_array($sidebar_widgets)){
                    foreach ($sidebar_widgets as $widget) {
                        if (strpos($widget, 'rokgallery_options-') !== false) {
                            $widget_id = intval(str_replace('rokgallery_options-', '', $widget));
                            $instance = $option[$widget_id];
                        }
                    }
                }
            }
        }

    }

    /**
     * @return array
     */
    function get_active_layouts()
    {

        $option = get_option('widget_rokgallery_options');
        $sidebar_widgets = wp_get_sidebars_widgets();
        $layouts = array();

        foreach ($sidebar_widgets as $sidebar => $sidebar_widgets) {
            if ($sidebar != 'wp_inactive_widgets') {
                if($sidebar_widgets && is_array($sidebar_widgets)){
                    foreach ($sidebar_widgets as $widget) {
                        if (strpos($widget, 'rokgallery_options-') !== false) {
                            $widget_id = intval(str_replace('rokgallery_options-', '', $widget));
                            $curr_inst = $option[$widget_id];
                            $layouts[] = array(
                                'layout' => $curr_inst['layout'],
                                'style' => $curr_inst['style']
                            );
                        }
                    }
                }
            }
        }
        return $layouts;
    }

    /**
     * @param $new_instance
     * @param $old_instance
     * @return mixed
     */
    function update($new_instance, $old_instance)
    {
        $global_instance = array();
        foreach (self::get_defaults() as $param) {
            if (array_key_exists($param, $new_instance)) {
                $global_instance[$param] = $new_instance[$param];
                unset($new_instance[$param]);
            }
        }
        update_option('widget_rokgallery_options', $global_instance);

        return $new_instance;
    }

    /**
     * @return array|mixed|void
     */
    function get_settings()
    {
		$global_options = get_option('widget_rokgallery_options');
        if (!empty($global_options) && !array_key_exists('_multiwidget', $global_options) ) {
            // old format, convert if single widget
            $global_options = wp_convert_widget_settings('rokgallery_options', 'widget_rokgallery_options', $global_options);
        }
        $new_options = array();
        if(!empty($global_options)){
            foreach($global_options as $gk => $global_option){
                if($gk=="_multiwidget"){
                    $new_options[$gk] = $global_option;
                }
                if ($global_option == false) {
                    $global_option = array();
                }
                $settings = parent::get_settings();
                foreach ($settings as $sk => $setting) {
                    if($gk==$sk && count($setting) > 0) {
                        $new_options[$gk] = array_merge($setting, $global_option);
                    }
                }
            }
        }
		return $new_options;
    }

    /**
     * @param $instance
     */
    function form($instance)
    {
        $defaults = $this->get_defaults();
        $instance = wp_parse_args((array)$instance, $defaults);
        foreach ($instance as $variable => $value)
        {
            $$variable = rg_cleanOutputVariable($variable, $value);
            $instance[$variable] = $$variable;
        }
        $instance['id_base'] = $this->id_base;
        $instance['number'] = $this->number;

        ob_start();
        echo RokCommon_Composite::get('rg_forms.widget')->load('widget_form.php', array('instance' => $instance));
        echo ob_get_clean();
    }


    /**
     * @param $args
     * @param $instance
     */
    function render($args, $instance)
    {
        if($instance['gallery_id']<1) {
            rc_e('No RokGallery has ben Selected');
            return;
        }
        //add thickbox
        wp_enqueue_script('jquery');
        wp_enqueue_style('thickbox');
        add_thickbox();

        $instance['widget_id'] = $this->number;
        $view = new RokGallery_Views_Widget_View();
        $view = $view->getView($instance);

        ob_start();
        echo $args['before_widget'];
        if ($instance['title'] != '') {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        if ($view->inline_js) {
            RokCommon_Header::addInlineScript($view->inline_js);
        }
        if ($view->inline_css) {
            RokCommon_Header::addInlineStyle($view->inline_css);
        }
        RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.js')->getUrl('moofx.js'));
        RokCommon_Header::addInlineScript(RokCommon_Composite::get('rokgallery.widget.' . $view->layout)->load('javascript.php', array('passed_params' => $view)));
        echo RokCommon_Composite::get('rg_views.widget')->load('default.php', array('view' => $view));
        echo $args['after_widget'];
        echo ob_get_clean();
    }

    /**
     * @param $args
     * @param $instance
     */
    function widget($args, $instance)
    {
        extract($args);
        $defaults = self::get_defaults();
        $instance = wp_parse_args((array)$instance, $defaults);
        foreach ($instance as $variable => $value)
        {
            $$variable = rg_cleanOutputVariable($variable, $value);
            $instance[$variable] = $$variable;
        }
        $this->render($args, $instance);
    }
}

add_action('widgets_init', array('RokGallery_Widgets_RokGallery', 'init'));
add_action('widgets_init', array('RokGallery_Widgets_RokGallery', 'rokgallery_init'));