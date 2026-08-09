# Betterlytics for WordPress

Official WordPress plugin for [Betterlytics](https://betterlytics.io) - privacy-first, open source analytics.

## Features

- **Traffic and Engagement** - Pageviews, visitors, sessions, time on page, scroll depth, referrers, campaigns, devices and geography
- **Automatic Script Injection** - Adds the lightweight (<1KB) Betterlytics analytics script to your site
- **Modern Admin UI** - Immersive, fast admin interface built with **Tailwind CSS v4**

## Installation

1. Download the plugin and upload to `/wp-content/plugins/betterlytics/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Settings > Betterlytics** to configure

## Configuration

### General Settings

| Setting             | Description                                         |
| ------------------- | --------------------------------------------------- |
| **Enable Tracking** | Toggle tracking on/off                              |
| **Site ID**         | Your unique Site ID from the Betterlytics dashboard |

### Automated Configuration (CI/CD)

Need to configure the plugin via deployment pipelines, WP-CLI, or infrastructure-as-code? See [CONFIGURATION.md](CONFIGURATION.md) for WP-CLI commands, environment variables, Ansible playbooks, and more.

## Developer API

### Sending Custom Events

You can send custom events from your theme or plugin:

```php
// Queue an event to be sent on page load
add_action( 'wp_footer', function() {
    if ( ! Betterlytics_Options::is_tracking_enabled() ) {
        return;
    }
    ?>
    <script>
    betterlytics.event('custom-event', {
        category: 'engagement',
        value: 123
    });
    </script>
    <?php
});
```

### Checking Tracking Status

```php
if ( Betterlytics_Options::is_tracking_enabled() ) {
    // Tracking is active
}
```

### Getting Plugin Options

```php
$options = Betterlytics_Options::get_options();
// Returns: site_id, server_url, script_url, enabled, track_outbound, etc.

// Get a single option
$site_id = Betterlytics_Options::get( 'site_id' );
```

## Requirements

- WordPress 5.0+
- PHP 7.4+
- pnpm 9.0+ (for developers)

## License

GPL v2 or later

## Contributing

Contributions are welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) for development setup, testing instructions, and guidelines.
