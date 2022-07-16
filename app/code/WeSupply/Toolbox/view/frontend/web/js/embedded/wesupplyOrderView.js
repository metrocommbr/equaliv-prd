define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    var options = {
        type: 'slide',
        responsive: true,
        innerScroll: false,
        modalClass: 'order-view-modal',
        buttons: [{
            text: $.mage.__('Close'),
            class: 'close-order-view',
            click: function () {
                this.closeModal();
            }
        }]
    };

    var createIframe = function (iframeUrl, platform) {
        var paramConcat = iframeUrl.indexOf('?') === -1 ? '?': '&';
        return $('<iframe />', {
            id: 'embedded-iframe-view',
            class: 'embedded-iframe',
            src: iframeUrl + paramConcat + 'platformType=' + platform,
            width: '100%',
            allowfullscreen: true,
            frameborder: 0,
            allow: 'geolocation',
            scrolling: 'no'
        });
    };

    var calcMaxHeight = function(iframe)
    {
        window.scrollTo(0, 0);
        return window.innerHeight - iframe.parentElement.offsetTop;
    };

    return {
        init: function(viewContainerId, platform)
        {
            var viewContainer = $('#' + viewContainerId);
            var orderView = modal(options, viewContainer);

            $('.action.view.iframe-view').on('click', function()
            {
                viewContainer.trigger('processStart');
                viewContainer.html(createIframe($(this).data('url'), platform));

                $('#embedded-iframe-view').on('load', function(){
                    var resizeTo = 0,
                        resized = false,
                        headerHeight = $('header').outerHeight(),
                        windowHeight = $(window).innerHeight(),
                        availableHeight =  windowHeight - headerHeight - 70,
                        isOldIE = (navigator.userAgent.indexOf("MSIE") !== -1);

                    iFrameResize({
                        log: false,
                        minHeight: windowHeight,
                        resizeFrom: 'parent',
                        scrolling: true,
                        inPageLinks: true,
                        autoResize: false,
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
                                messageData.iframe.style.visibility = 'visible';
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

                    viewContainer
                        .trigger('processStop')
                        .height($('.order-view-modal').height());
                    orderView.openModal();

                    setTimeout(function() {
                        if (!resized) {
                            $(this).css({'height': '1000px', 'visibility': 'visible'});
                        }
                    }, 600);
                });
            });
        }
    }
});
