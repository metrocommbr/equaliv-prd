(function (factory) {
    'use strict';

    if (typeof define === 'function' && define.amd) {
        define([
            'jquery'
        ], factory);
    } else {
        factory(window.jQuery);
    }
}(function ($) {
    'use strict';

    var $window = $(window);

    $.fn.themePluginFloatElement = function(options) {
        var settings = {
            startPos: "top",
            speed: 3,
            horizontal: false,
            transition: false
        };
        var $el, $options;

        function initialize(t, options) {
            return t.data("__floatElement") ? this : ($el = t,
            setData(),
            setOptions(options),
            build(),
            this)
        }

        function setData() {
            return $el.data("__floatElement")
        }

        function setOptions(options) {
            return $options = $.extend(!0, {}, settings, options, {
                wrapper: $el
            })
        }

        function build() {
            var t, o = $options.wrapper, s = $(window);
            return $options.style && o.attr("style", $options.style),
            s.width() > 767 && ("none" == $options.startPos ? t = "" : "top" == $options.startPos ? (o.css({
                top: 0
            }),
            t = "") : (o.css({
                bottom: 0
            }),
            t = "-"),
            $options.transition && o.css({
                transition: "ease transform 500ms"
            }),
            movement(t),
            s.on("scroll", function() {
                movement(t)
            }))
        }

        function movement(t) {
            var i = $($options.wrapper)
              , o = $(window)
              , s = o.scrollTop()
              , n = 100 * (i.offset().top - s) / o.height();
            $options.horizontal ? i.css({
                transform: "translate3d(" + t + n / $options.speed + "%, " + t + n / $options.speed + "%, 0)"
            }) : i.css({
                transform: "translate3d(0, " + t + n / $options.speed + "%, 0)"
            })
        }

        initialize(this, options);

        return this;
    }
}));