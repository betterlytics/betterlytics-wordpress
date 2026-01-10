(function() {
    'use strict';

    var config = window.betterlyticsEvents || {};

    // File download tracking
    if (config.trackDownloads) {
        var downloadExtensions = /\.(pdf|zip|doc|docx|xls|xlsx|ppt|pptx|exe|dmg|tar|gz|rar)$/i;
        document.addEventListener('click', function(e) {
            var link = e.target.closest('a[href]');
            if (!link) return;

            if (downloadExtensions.test(link.href)) {
                window.betterlytics && betterlytics.event('file-download', {
                    url: link.href,
                    filename: link.href.split('/').pop()
                });
            }
        });
    }

    // CSS class event tracking (data-betterlytics-event attribute)
    if (config.trackCssEvents) {
        document.addEventListener('click', function(e) {
            var element = e.target.closest('[data-betterlytics-event]');
            if (!element) return;

            var eventName = element.getAttribute('data-betterlytics-event');
            if (eventName) {
                window.betterlytics && betterlytics.event(eventName, {
                    element: element.tagName.toLowerCase(),
                    text: element.innerText.substring(0, 100)
                });
            }
        });
    }
})();
