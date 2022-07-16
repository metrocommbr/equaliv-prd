define([
    'jquery'
], function ($) {
    'use strict';

    var createIframe = function (iframeUrl, iframeId) {
        return $('<iframe />', {
            id: iframeId,
            class: 'embedded-iframe',
            src: iframeUrl,
            width: '100%',
            allowfullscreen: true,
            frameborder: 0,
            allow: 'geolocation',
            scrolling: 'no'
        });
    };

    var calcMaxHeight = function(iframe)
    {
        return window.innerHeight - iframe.parentElement.offsetTop;
    };

    return {
        load: function(iframeUrl, containerId)
        {
            var iframeId = containerId + '-iframe';
            var viewContainer = $('#' + containerId);
            var loadingContainer = $('.loading-container');

            if (typeof iframeUrl === 'undefined' || iframeUrl === '') {
                loadingContainer.hide();
                viewContainer.show();

                return;
            }

            viewContainer.trigger('processStart');
            viewContainer.html(createIframe(iframeUrl, iframeId));

            $('#' + iframeId).on('load', function(){
                $(this).css('visibility', 'visible');
                var resizeTo = 0,
                    resized = false,
                    headerHeight = $('header').outerHeight(),
                    windowHeight = $(window).innerHeight(),
                    availableHeight =  windowHeight - headerHeight - 70,
                    isOldIE = (navigator.userAgent.indexOf("MSIE") !== -1);

                iFrameResize({
                    log: false,
                    minHeight: availableHeight,
                    resizeFrom: 'parent',
                    scrolling: true,
                    inPageLinks: true,
                    autoResize: true,
                    heightCalculationMethod: isOldIE ? 'max' : 'bodyScroll',
                    onInit: function(iframe) {
                        iframe.style.height = availableHeight + 'px';
                    },
                    onResized: function(messageData) {
                        setTimeout(function() {
                            if (resizeTo) {
                                resized = true;
                                messageData.iframe.style.height = resizeTo + 'px';
                                $('html, body').animate({ scrollTop: 0 }, 'fast');
                            }
                            // messageData.iframe.style.visibility = 'visible';
                        }, 300);
                    },
                    onMessage: function(messageData) {
                        if (messageData.message.event === 'resize') {
                            resizeTo = calcMaxHeight(messageData.iframe);
                        }
                        if (messageData.message.event === 'stop') {
                            resizeTo = 0;
                        }
                    }
                }, '.embedded-iframe');

                loadingContainer.hide();
                viewContainer.trigger('processStop').show();

                setTimeout(function() {
                    if (!resized) {
                        $(this).css({'height': '1000px', 'visibility': 'visible'});
                    }
                }, 600);
            });
        }
    }
});
