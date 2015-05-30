<?php
/**
 * @version   $Id: colorchooser.php 10867 2013-05-30 04:04:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
class RokGallery_Forms_Fields_ColorChooser
{
    
    /**
     * @var
     */
    var $html;

    /**
     * @param null $instance
     * @param $fieldName
     * @param $fieldId
     * @param $fieldValue
     * @return string
     */
    public static function getInput($fieldName, $fieldId, $fieldValue, $transparent=1)
    {
        global $stylesList;
        $html = '';

        //TODO have to do this here until rokcommon header can load files after header has run
        RokCommon_Header::addStyle(RokCommon_Composite::get('rg_assets.moorainbow')->getUrl('mooRainbow.css'));
        RokCommon_Header::addScript(RokCommon_Composite::get('rg_assets.moorainbow')->getUrl('mooRainbow.js'));
        
        $init = self::rainbowInit();
        RokCommon_Header::addInlineScript($init);
        
        $script = self::newRainbow($fieldId, $transparent);
        RokCommon_Header::addInlineScript($script);

        $html .= "\n" . '<input class="picker-input text-color" id="' . $fieldId . '" name="' . $fieldName . '" type="text" size="8" maxlength="11" value="' . $fieldValue . '" />';
        $html .= "\n" . '<div class="picker" id="myRainbow_' . $fieldId . '_input">';
        $html .= "\n" . '<div class="overlay' . (($fieldValue == 'transparent') ? ' overlay-transparent': '') . '" style="background-color: ' . $fieldValue . '">';
        $html .= "\n" . '</div></div>';
        $html .= "\n" . '<div style="clear:both;"></div>';

        return $html;
    }

    /**
     * @param $id
     * @param $transparent
     * @return string
     */
    protected function newRainbow($id, $transparent)
    {
        return "
		var r_" . $id . ";
		window.addEvent('domready', function() {
			document.id('" . $id . "').getParent().addEvents({
				'mouseenter': f_" . $id . ",
				'mouseleave': function(){
					this.removeEvent('mouseenter', f_" . $id . ");
				}
			});
		});

		var f_" . $id . " = function(){
			var input = document.id('" . $id . "');
			r_" . $id . " = new MooRainbow('myRainbow_" . $id . "_input', {
				id: 'myRainbow_" . $id . "',
				startColor: document.id('" . $id . "').get('value').hexToRgb(true) || [255, 255, 255],
				imgPath: '" . ROKGALLERY_PLUGIN_URL . "/assets/moorainbow/images/',
				transparent: " . $transparent . ",
				onChange: function(color) {
					if (color == 'transparent') {
						input.getNext().getFirst().addClass('overlay-transparent').setStyle('background-color', 'transparent');
						input.value = 'transparent';
					}
					else {
						input.getNext().getFirst().removeClass('overlay-transparent').setStyle('background-color', color.hex);
						input.value = color.hex;
					}

					if (this.visible) this.okButton.focus();
				}
			});

			r_" . $id . ".okButton.setStyle('outline', 'none');
			document.id('myRainbow_" . $id . "_input').addEvent('click', function() {
				(function() {r_" . $id . ".okButton.focus()}).delay(10);
			});
			input.addEvent('keyup', function(e) {
				if (e) e = new Event(e);
				if ((this.value.length == 4 || this.value.length == 7) && this.value[0] == '#') {
					var rgb = new Color(this.value);
					var hex = this.value;
					var hsb = rgb.rgbToHsb();
					var color = {
						'hex': hex,
						'rgb': rgb,
						'hsb': hsb
					}
					r_" . $id . ".fireEvent('onChange', color);
					r_" . $id . ".manualSet(color.rgb);
				};
			});

			input.getNext().getFirst().setStyle('background-color', r_" . $id . ".sets.hex);
			rainbowLoad('myRainbow_" . $id . "');
		};\n";
    }

    /**
     * @return string
     */
    protected function rainbowInit()
    {
        return "var rainbowLoad = function(name, hex) {
				if (hex) {
					var n = name.replace('params', '');
					document.id(n+'_input').getPrevious().value = hex;
					document.id(n+'_input').getFirst().setStyle('background-color', hex);
				}
			};
		";
    }
}
