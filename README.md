# Betterlytics for WordPress

Official WordPress plugin for [Betterlytics](https://betterlytics.io) - privacy-first, open source analytics.

## Features

- **Automatic Script Injection** - Adds the lightweight (<1KB) Betterlytics tracking script to your site
- **Self-Hosting Support** - Works with both Betterlytics Cloud and self-hosted instances
- **WordPress Hooks Integration** - Map WordPress actions to Betterlytics custom events
- **Privacy Controls** - Option to exclude logged-in users from tracking

## Installation

1. Download the plugin and upload to `/wp-content/plugins/betterlytics/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Settings > Betterlytics** to configure

## Configuration

### General Settings

| Setting | Description |
|---------|-------------|
| **Enable Tracking** | Toggle tracking on/off |
| **Site ID** | Your unique Site ID from the Betterlytics dashboard |
| **Server URL** | Tracking endpoint (default: `https://betterlytics.io/track`) |
| **Script URL** | Analytics script URL (default: `https://betterlytics.io/analytics.js`) |
| **Track Logged-in Users** | Include/exclude logged-in users from analytics |

### Self-Hosting

If you're self-hosting Betterlytics, update the Server URL and Script URL to point to your instance:

- **Server URL**: `https://your-instance.com/track`
- **Script URL**: `https://your-instance.com/analytics.js`

## WordPress Hooks Integration

The plugin allows you to map WordPress actions to Betterlytics events. When a WordPress hook fires, it automatically sends a custom event.

### Adding a Hook

1. Go to **Settings > Betterlytics**
2. Scroll to "WordPress Hooks Integration"
3. Click "Add New Hook"
4. Enter the WordPress hook name and your desired event name
5. Click "Save Hooks"

### Common Hooks

| WordPress Hook | Description |
|----------------|-------------|
| `wp_login` | User logs in |
| `user_register` | New user registration |
| `comment_post` | New comment posted |
| `woocommerce_thankyou` | WooCommerce order complete |
| `woocommerce_add_to_cart` | Product added to cart |
| `wpcf7_mail_sent` | Contact Form 7 submission |
| `gform_after_submission` | Gravity Forms submission |
| `wpforms_process_complete` | WPForms submission |

### Event Properties

The plugin automatically includes relevant properties based on the hook type:

- **WooCommerce**: `order_id`, `product_id`, `quantity`
- **Forms**: `form_id`
- **Comments**: `comment_id`

No personally identifiable information (PII) is ever sent.

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
// Returns: site_id, server_url, script_url, enabled, track_logged_in, hooks

// Get a single option
$site_id = Betterlytics_Options::get( 'site_id' );
```

## Requirements

- WordPress 6.0+
- PHP 7.4+

## License

GPL v2 or later

## Contributing

Contributions are welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) for development setup, testing instructions, and guidelines.
