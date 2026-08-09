=== Betterlytics ===
Contributors: betterlytics
Tags: analytics, privacy, gdpr, cookieless, web-analytics
Requires at least: 6.3
Tested up to: 7.0
Stable tag: 1.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Privacy-first, cookieless analytics for WordPress. See visitors, pageviews, sessions, referrers, campaigns, devices and geography. No code needed.

== Description ==

[Betterlytics](https://betterlytics.io) is an open-source, cookieless analytics platform. This is the official plugin that connects it to WordPress: install, paste in your Site ID, and data starts appearing in your dashboard. No theme edits, no code snippets, and since Betterlytics sets no cookies, most sites will not need a consent banner for it either.

= What you get =

Once your Site ID is saved and data collection is switched on, your dashboard fills in with pageviews, visitors and sessions, plus time on page, scroll depth, referrers, campaigns, devices and geography. Nothing to configure beyond the Site ID.

= What the plugin does =

* Adds the Betterlytics analytics script (under 1KB, loaded async) to your site automatically
* Walks you through setup with a short wizard on the settings page
* Lets you switch on extra measurements per feature: outbound link clicks and Core Web Vitals
* Records clicks on any element you mark with a single HTML attribute, without writing JavaScript
* Works with Betterlytics Cloud or your own self-hosted instance, with both the server and script URLs configurable

Core Web Vitals and click events stay off until you switch them on. Outbound links record the destination domain by default, and can be set to full URLs or switched off.

= Privacy =

Betterlytics collects no personally identifiable information and sets no cookies. That is how it is built, not a setting you have to find. With no cookies to consent to, most sites will not need a cookie notice or a consent management plugin for Betterlytics, though what your site needs overall still depends on the other services you run. The platform is open source, so you can audit exactly what is collected, or run it entirely on your own servers.

= Resources =

* [Documentation](https://betterlytics.io/docs)
* [Live demo dashboard](https://betterlytics.io/demo)
* [GitHub repository](https://github.com/betterlytics/betterlytics)
* [Discord community](https://discord.gg/vwqSvPn6sP)

== Installation ==

= From your WordPress dashboard (recommended) =

1. Go to **Plugins > Add New** in your WordPress admin
2. Search for **Betterlytics**
3. Click **Install Now** and then **Activate**
4. Go to **Settings > Betterlytics** - the Home tab greets you with a setup wizard
5. Paste in your Site ID from your [Betterlytics dashboard](https://betterlytics.io/dashboard)
6. Toggle on **Enable tracking** and click **Save Settings**

= Manual installation =

1. Download the plugin zip from [wordpress.org/plugins/betterlytics](https://wordpress.org/plugins/betterlytics/)
2. Go to **Plugins > Add New > Upload Plugin** and upload the zip (or extract the `betterlytics` folder to `/wp-content/plugins/`)
3. Activate the plugin through the **Plugins** menu, then follow steps 4–6 above

= Getting a Site ID =

1. Sign up at [betterlytics.io](https://betterlytics.io/onboarding)
2. Add your website
3. Copy your Site ID from the dashboard

== Frequently Asked Questions ==

= Do I need a Betterlytics account? =

Yes, you need a Site ID from Betterlytics to use this plugin. You can sign up for free at [betterlytics.io](https://betterlytics.io/onboarding).

= What data does Betterlytics collect? =

Pageviews, visitors and sessions, plus time on page, scroll depth, referrers, campaign parameters, device and browser, and country. Visitors are counted without cookies and without storing personal data, so there is no identifier kept on the visitor's device. The External Services section below lists exactly what each request contains, and the [documentation](https://betterlytics.io/docs) covers how each metric is calculated.

= Does this plugin use cookies? =

No. Betterlytics is cookieless by design, so there is nothing stored on your visitors' devices.

= Is this GDPR compliant? =

Betterlytics is built to avoid what GDPR is concerned with. No personally identifiable information is collected and no cookies are set, so there is nothing stored on your visitors' devices to ask consent for. See the [privacy policy](https://betterlytics.io/privacy) for the full detail.

= Can I self-host Betterlytics? =

Yes. Betterlytics is open source. You can configure the plugin to point to your own self-hosted instance by updating the Server URL and Script URL in the settings.

== External Services ==

This plugin connects to the Betterlytics analytics service to collect analytics data about visits to your site.

**What is sent:**

* The page address, and the address of the page the visitor arrived from
* Campaign parameters such as `utm_source` and `utm_campaign`
* Country and region, worked out from visitor's request
* Device, browser and operating system, read from the browser's user agent, plus screen resolution
* How far down the page the visitor scrolled, and how long they spent on it
* Clicks on links leading off your site, recording the destination domain by default
* Core Web Vitals and custom click events, if you switch those options on
* Your Site ID, so the data reaches the right dashboard

No personally identifiable information (PII) is collected, and no cookies are set.

**Service Information:**

* Website: [https://betterlytics.io](https://betterlytics.io)
* Privacy Policy: [https://betterlytics.io/privacy](https://betterlytics.io/privacy)
* Terms of Service: [https://betterlytics.io/terms](https://betterlytics.io/terms)

The analytics script is loaded from an external CDN or your configured server URL.

By enabling tracking, you consent to data being sent to Betterlytics servers (or your self-hosted instance).

== Screenshots ==

1. Settings page: configure your Site ID and data collection options
2. Events configuration: enable browser event options

== Changelog ==

= 1.1.0 =
* Removed 404 error page tracking
* Removed site search tracking
* Removed file download tracking

= 1.0.0 =
* Initial release
* Automatic tracking script injection
* WordPress hooks integration

== Upgrade Notice ==

= 1.1.0 =
404, site search, and file download tracking have been removed to simplify the plugin. All other tracking features are unaffected; previously saved settings for the removed features are ignored.

= 1.0.0 =
Initial release of Betterlytics for WordPress.
