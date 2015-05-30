edButtons[edButtons.length] = new edButton('ed_rokgallery', 'rokgallery', '', '', 'r');

if (typeof MooTools != 'undefined'){
window.addEvent('domready', function () {
    if ($('ed_rokgallery')) {
        $('ed_rokgallery').addEvent('mouseover', function () {
            if ($('rokgallery_link') == null) {
                var url = ajaxurl + '?action=rokgallery_gallerypicker&TB_iframe=true&height=600&width=555&modal=false';
                var newLinknew = Element('a#rokgallery_link').wraps(this);
                $('rokgallery_link').setProperty('href', url);
                $('rokgallery_link').setProperty('class', 'thickbox');
            }
        })
    }
});
}
