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

## Development

### Requirements

- [Docker](https://docs.docker.com/get-docker/) & Docker Compose

### Quick Start

```bash
# Copy environment file and adjust if needed
cp .env.example .env

# Start WordPress development environment
docker compose up -d
```

WordPress will be available at **http://localhost:8888** (or the port you set in `.env`).

Default WordPress admin credentials:
- **Username**: `admin`
- **Password**: `admin`

The plugin is automatically activated on first run.

### Available Commands

```bash
# Start development environment
docker compose up -d

# Stop development environment
docker compose down

# View logs
docker compose logs -f

# Run PHP linting (PHPCS)
docker compose --profile lint run --rm lint

# Auto-fix linting issues
docker compose --profile lint-fix run --rm lint-fix

# Run PHPUnit tests
docker compose --profile test run --rm test

# Build plugin zip
docker compose --profile build run --rm build

# Clean up everything (including volumes)
docker compose down -v
```

### Running Tests

```bash
# Run all tests
docker compose --profile test run --rm test

# Run specific test file
docker compose --profile test run --rm test vendor/bin/phpunit tests/test-options.php

# Run with verbose output
docker compose --profile test run --rm test vendor/bin/phpunit --verbose
```

### Coding Standards

This plugin follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/). The configuration is in `phpcs.xml.dist`.

```bash
# Check coding standards
docker compose --profile lint run --rm lint

# Auto-fix issues
docker compose --profile lint-fix run --rm lint-fix
```

### Project Structure

```
betterlytics-wordpress/
├── admin/                    # Admin-specific functionality
│   ├── css/                  # Admin stylesheets
│   ├── js/                   # Admin JavaScript
│   ├── partials/             # Admin view templates
│   └── class-betterlytics-admin.php
├── includes/                 # Core plugin classes
│   ├── class-betterlytics.php
│   ├── class-betterlytics-activator.php
│   ├── class-betterlytics-deactivator.php
│   ├── class-betterlytics-hooks.php
│   ├── class-betterlytics-i18n.php
│   ├── class-betterlytics-loader.php
│   └── class-betterlytics-options.php
├── languages/                # Translation files
├── public/                   # Public-facing functionality
│   └── class-betterlytics-public.php
├── tests/                    # PHPUnit tests
├── .github/workflows/        # GitHub Actions CI
├── betterlytics.php          # Main plugin file
├── uninstall.php             # Cleanup on uninstall
├── docker-compose.yml        # Docker development environment
├── Dockerfile.test           # Test runner container
├── composer.json             # PHP dependencies
├── phpcs.xml.dist            # PHPCS configuration
└── phpunit.xml.dist          # PHPUnit configuration
```

## Requirements

- WordPress 6.0+
- PHP 7.4+

## License

GPL v2 or later

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure your code follows WordPress Coding Standards and includes tests where applicable.
