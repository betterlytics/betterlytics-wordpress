# Contributing to Betterlytics for WordPress

Thank you for your interest in contributing! This guide will help you get set up for development.

## Requirements

- [Docker](https://docs.docker.com/get-docker/) & Docker Compose
- [pnpm](https://pnpm.io/) (for CSS development)

## Quick Start

```bash
# Copy environment file and adjust if needed
cp .env.example .env

# Start WordPress development environment
docker compose up -d

# Install CSS dependencies
pnpm install
```

WordPress will be available at **http://localhost:8888** (or the port you set in `.env`).

Default WordPress admin credentials:
- **Username**: `admin`
- **Password**: `admin`

The plugin is automatically activated on first run.

## Available Commands

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

# --- CSS Development ---

# Install dependencies
pnpm install

# Build CSS once
pnpm run build:css

# Watch for CSS changes during development
pnpm run watch:css
```

## Running Tests

```bash
# Run all tests
docker compose --profile test run --rm test

# Run specific test file
docker compose --profile test run --rm test vendor/bin/phpunit tests/test-options.php

# Run with verbose output
docker compose --profile test run --rm test vendor/bin/phpunit --verbose
```

## Demo Environment (WooCommerce Integration Testing)

The `demo` profile sets up a full testing environment with test pages for verifying event tracking.

### Starting the Demo Environment

```bash
# Start WordPress with WooCommerce and demo pages
docker compose --profile demo up -d

# Watch the setup complete
docker compose logs -f demo-setup
```

### What the Demo Setup Creates

- **Test Page** - Available at `/betterlytics-test/` with:
  - Connection status indicator
  - Manual event trigger buttons
  - WordPress hook triggers (login, comments)
  - Real-time event log

### Testing with Local Betterlytics

If you're running [Betterlytics](https://github.com/betterlytics/betterlytics) locally:

```bash
# Terminal 1: Start Betterlytics (in the betterlytics repo)
docker compose up -d

# Terminal 2: Start WordPress with demo (in this repo)
docker compose --profile demo up -d
```

Configure the plugin at **Settings > Betterlytics**:
- **Site ID**: Get from your Betterlytics dashboard (http://localhost:3000)
- **Server URL**: `http://localhost:3001/event`
- **Script URL**: `http://localhost:3006/analytics.js`
- **Enabled**: Check this box

Then visit http://localhost:8888/betterlytics-test/ to test events.



### Demo Cleanup

```bash
# Stop demo environment
docker compose --profile demo down

# Full reset (removes all data)
docker compose --profile demo down -v
```

## CSS Development

The plugin uses **Tailwind CSS v4** for its admin interface. To make changes to the styling:

1.  **Edit the Source**: Modify `admin/css/betterlytics-admin.src.css`. This file contains the Tailwind directives and any custom CSS components.
2.  **Run the Build**: Use `pnpm run build:css` (or just `pnpm build:css`) to compile the source file into `admin/css/betterlytics-admin.css`.
3.  **Active Development**: Run `pnpm run watch:css` in a separate terminal to automatically recompile CSS whenever you save a PHP template or the source CSS file.

> [!IMPORTANT]
> Do not modify `admin/css/betterlytics-admin.css` directly, as it is overwritten during the build process.

## Coding Standards

This plugin follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/). The configuration is in `phpcs.xml.dist`.

```bash
# Check coding standards
docker compose --profile lint run --rm lint

# Auto-fix issues
docker compose --profile lint-fix run --rm lint-fix
```

## Project Structure

```
betterlytics-wordpress/
├── admin/                    # Admin-specific functionality
│   ├── css/                  # Admin stylesheets
│   ├── js/                   # Admin JavaScript
│   ├── components/           # Component-based UI Architecture
│   │   ├── ui/               # Base reusable components (Card, Button, etc.)
│   │   └── views/            # Feature-specific page views
│   │       └── settings/     # Settings tab views
│   ├── partials/             # Admin view templates (Legacy/Fallback)
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
├── demo/                     # Demo environment (not included in build)
│   └── mu-plugins/           # Must-use plugins for testing
├── .github/workflows/        # GitHub Actions CI
├── betterlytics.php          # Main plugin file
├── uninstall.php             # Cleanup on uninstall
├── docker-compose.yml        # Docker development environment
├── Dockerfile.test           # Test runner container
├── composer.json             # PHP dependencies
├── package.json              # JS dependencies (Tailwind, PostCSS)
├── pnpm-lock.yaml            # pnpm lockfile
├── phpcs.xml.dist            # PHPCS configuration
├── postcss.config.js         # PostCSS configuration
├── tailwind.config.js        # Tailwind configuration
└── phpunit.xml.dist          # PHPUnit configuration
```

## Submitting Changes

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run linting: `docker compose --profile lint run --rm lint`
5. Run tests: `docker compose --profile test run --rm test`
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

Please ensure your code follows WordPress Coding Standards and includes tests where applicable.
