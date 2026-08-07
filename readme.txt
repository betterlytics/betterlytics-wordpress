=== Betterlytics ===
Contributors: betterlytics
Tags: analytics, privacy, gdpr, cookieless, statistics
Requires at least: 6.3
Tested up to: 7.0
Stable tag: 1.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Privacy-first, cookieless analytics for WordPress. Easily add Betterlytics tracking to your site without editing code.

== Description ==

[Betterlytics](https://betterlytics.io) is an open-source, cookieless analytics platform. This is the official plugin that connects it to WordPress: install, paste in your Site ID, and data starts appearing in your dashboard. No theme edits, no code snippets, and since Betterlytics doesn't use cookies, no consent banner either.

= What the plugin does =

* Adds the Betterlytics tracking script (under 1KB, loaded async) to your site automatically
* Walks you through setup with a short wizard on the settings page
* Lets you switch on extra event tracking per feature: outbound link clicks and Core Web Vitals
* Tracks clicks on any element you mark with a single HTML attribute, without writing JavaScript
* Works with Betterlytics Cloud or your own self-hosted instance — both the server and script URLs are configurable

All event tracking features are opt-in and stay disabled until you enable them.

= Privacy =

Betterlytics collects no personally identifiable information and uses no cookies, so it's GDPR compliant by design. Your visitors get privacy without cookie notices, and you get analytics without a consent management plugin. The platform itself is open source, so you can audit exactly what is collected — or run it entirely on your own servers.

= Resources =

* [Documentation](https://betterlytics.io/docs)
* [Live demo dashboard](https://betterlytics.io/share/cmcxux3l8000ar9081uwprkwz)
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

= Does this plugin use cookies? =

No. Betterlytics is cookieless by design. No consent banners are required.

= Is this GDPR compliant? =

Yes. Betterlytics does not collect personally identifiable information and does not use cookies, making it GDPR compliant without requiring consent.

= Can I self-host Betterlytics? =

Yes. Betterlytics is open source. You can configure the plugin to point to your own self-hosted instance by updating the Server URL and Script URL in the settings.

== External Services ==

This plugin connects to the Betterlytics analytics service to track website visits.

**What is sent:**

* Page views (URL, referrer, browser information)
* Custom events (if configured)
* No personally identifiable information (PII) is collected

**Service Information:**

* Website: [https://betterlytics.io](https://betterlytics.io)
* Privacy Policy: [https://betterlytics.io/privacy](https://betterlytics.io/privacy)
* Terms of Service: [https://betterlytics.io/terms](https://betterlytics.io/terms)

The analytics script is loaded from an external CDN or your configured server URL.

By enabling tracking, you consent to data being sent to Betterlytics servers (or your self-hosted instance).

== Screenshots ==

1. Settings page — Configure your Site ID and tracking options
2. Events configuration — Enable browser event tracking options

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
