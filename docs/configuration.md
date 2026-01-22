# Automated Configuration

The Betterlytics plugin stores all settings in a single WordPress option (`betterlytics_options`). This makes it easy to configure via deployment pipelines, WP-CLI, or provisioning scripts - no manual UI clicks required.

## Option Structure

```json
{
	"site_id": "your-site-id",
	"server_url": "https://betterlytics.io/event",
	"script_url": "https://betterlytics.io/analytics.js",
	"enabled": true,
	"track_web_vitals": false,

	"track_404": { "enabled": false },
	"track_search": { "enabled": false, "include_query": false },
	"track_outbound": { "mode": "domain" },
	"track_downloads": { "enabled": false },
	"track_css_events": { "enabled": false }
}
```

### track_search Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | boolean | `false` | Enable site search tracking |
| `include_query` | boolean | `false` | Include search query term with events |

## WP-CLI

Configure the plugin directly from your deployment scripts:

```bash
# Set all options at once (JSON)
wp option update betterlytics_options '{"site_id":"abc123","server_url":"https://analytics.example.com/track","script_url":"https://analytics.example.com/analytics.js","enabled":true}' --format=json

# Or pipe from a config file
wp option update betterlytics_options --format=json < betterlytics-config.json

# Update a single value (get current, modify, set back)
wp option get betterlytics_options --format=json | \
  jq '.site_id = "new-site-id"' | \
  wp option update betterlytics_options --format=json
```

## Environment Variables

For container deployments, you can configure from environment variables using a must-use plugin.

Create `wp-content/mu-plugins/betterlytics-env-config.php`:

```php
<?php
/**
 * Configure Betterlytics from environment variables.
 */
add_action( 'init', function() {
    // Only run once
    if ( get_option( 'betterlytics_configured_by_env' ) ) {
        return;
    }

    $site_id = getenv( 'BETTERLYTICS_SITE_ID' );
    if ( ! $site_id ) {
        return;
    }

    update_option( 'betterlytics_options', [
        'site_id'         => $site_id,
        'server_url'      => getenv( 'BETTERLYTICS_SERVER_URL' ) ?: 'https://betterlytics.io/event',
        'script_url'      => getenv( 'BETTERLYTICS_SCRIPT_URL' ) ?: 'https://betterlytics.io/analytics.js',
        'enabled'         => filter_var( getenv( 'BETTERLYTICS_ENABLED' ) ?: 'true', FILTER_VALIDATE_BOOLEAN )
    ]);

    update_option( 'betterlytics_configured_by_env', true );
}, 1 );
```

Then set environment variables in your Docker Compose, Kubernetes, or CI:

```yaml
# docker-compose.yml
services:
    wordpress:
        environment:
            BETTERLYTICS_SITE_ID: "your-site-id"
            BETTERLYTICS_SERVER_URL: "https://analytics.example.com/track"
            BETTERLYTICS_SCRIPT_URL: "https://analytics.example.com/analytics.js"
            BETTERLYTICS_ENABLED: "true"
```

## Config File Approach

Keep a `betterlytics-config.json` in your deployment repo for reproducible configuration:

```json
{
	"site_id": "production-site-id",
	"server_url": "https://analytics.example.com/track",
	"script_url": "https://analytics.example.com/analytics.js",
	"enabled": true
}
```

Apply during deployment:

```bash
wp option update betterlytics_options --format=json < betterlytics-config.json
```

## Ansible

```yaml
- name: Configure Betterlytics plugin
  command: >
      wp option update betterlytics_options
      '{{ betterlytics_config | to_json }}'
      --format=json
      --path=/var/www/html
  become_user: www-data
  vars:
      betterlytics_config:
          site_id: "{{ betterlytics_site_id }}"
          server_url: "{{ betterlytics_server_url }}"
          script_url: "{{ betterlytics_script_url }}"
          enabled: true
```

## Terraform (with WP-CLI provisioner)

```hcl
resource "null_resource" "configure_betterlytics" {
  provisioner "remote-exec" {
    inline = [
      "wp option update betterlytics_options '${jsonencode({
        site_id         = var.betterlytics_site_id
        server_url      = var.betterlytics_server_url
        script_url      = var.betterlytics_script_url
        enabled         = true
      })}' --format=json --path=/var/www/html"
    ]
  }
}
```

## Docker Development

For local Docker development, the demo profile automatically configures the plugin. See `demo/config.json`:

```json
{
	"server_url": "http://host.docker.internal:3001/event",
	"script_url": "http://localhost:3006/analytics.js"
}
```

Note: `host.docker.internal` allows the WordPress container to reach services running on your host machine.

## Resetting Configuration

To re-apply configuration (e.g., after changing your config file):

```bash
# Remove the "already configured" flag
wp option delete betterlytics_configured_by_env

# Or just force update the options directly
wp option update betterlytics_options --format=json < betterlytics-config.json
```
