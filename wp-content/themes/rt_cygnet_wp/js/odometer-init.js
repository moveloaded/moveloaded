;((function(){

var isElementInViewport = function(el) {
    var rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

window.addEvents({
    scroll: function(){
        var odometers = document.getElements('.odometer'), value, instances = {};
        odometers.forEach(function(odometer, idx){
            odometer = document.id(odometer);
            if (!instances['o-' + idx] && isElementInViewport(odometer)) {
                value = odometer.getProperty('data-odometer-value');
                instances['o-' + idx] = { i: new Odometer({el: odometer}), v: value };
                setTimeout(function(){
                    instances['o-' + idx].i.update(instances['o-' + idx].v || 0);
                }, 300);
            }
        });
    },
    domready: function(){
        this.fireEvent('scroll');
    }
});

})());
