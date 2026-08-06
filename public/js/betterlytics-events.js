(function () {
	"use strict";

	var config = window.betterlyticsEvents || {};

	// Custom HTML attribute event tracking (data-betterlytics-event attribute)
	if (config.trackCustomHtmlAttr) {
		document.addEventListener("click", function (e) {
			var element = e.target.closest("[data-betterlytics-event]");
			if (!element) return;

			var eventName = element.getAttribute("data-betterlytics-event");
			if (eventName) {
				window.betterlytics &&
					betterlytics.event(eventName, {
						element: element.tagName.toLowerCase(),
						text: element.innerText.substring(0, 100),
					});
			}
		});
	}
})();
