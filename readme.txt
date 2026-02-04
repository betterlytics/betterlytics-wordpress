=== Betterlytics ===
Contributors: betterlytics
Tags: analytics, privacy, gdpr, cookieless, statistics
Requires at least: 6.3
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Privacy-first, cookieless analytics for WordPress. Easily add Betterlytics tracking to your site without editing code.

== Description ==

**Betterlytics for WordPress** is the official plugin for [Betterlytics](https://betterlytics.io) — a privacy-first, cookieless analytics platform that's GDPR compliant out of the box.

This plugin helps you quickly set up Betterlytics on your WordPress site without editing theme files or adding code manually.

= Features =

* **Easy Setup** — Add your Site ID and start tracking in seconds
* **No Code Required** — Automatically injects the lightweight tracking script
* **WordPress Hooks Integration** — Map WordPress actions to custom analytics events
* **Self-Hosting Support** — Works with both Betterlytics Cloud and self-hosted instances

= Why Betterlytics? =

* **Privacy-first** — No cookies, no consent banners required
* **GDPR Compliant** — Respects visitor privacy by design
* **Lightweight** — Under 1KB tracking script
* **Open Source** — Transparent, community-driven development

= Links =

* [Betterlytics Website](https://betterlytics.io)
* [Documentation](https://betterlytics.io/docs)
* [GitHub Repository](https://github.com/betterlytics/betterlytics)
* [Live Demo](https://betterlytics.io/share/cmcxux3l8000ar9081uwprkwz)

== Installation ==

1. Upload the `betterlytics` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Go to **Settings > Betterlytics**
4. Enter your Site ID from your [Betterlytics dashboard](https://betterlytics.io)
5. Enable tracking and save

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
2. Events configuration — Set up WordPress hooks for custom event tracking

== Changelog ==

= 1.0.0 =
* Initial release
* Automatic tracking script injection
* WordPress hooks integration

== Upgrade Notice ==

= 1.0.0 =
Initial release of Betterlytics for WordPress.
