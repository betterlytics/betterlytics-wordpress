#!/bin/bash
set -e

WP_TESTS_DIR=${WP_TESTS_DIR:-/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR:-/tmp/wordpress}

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
while ! mysqladmin ping -h"$WORDPRESS_DB_HOST" --silent; do
    sleep 1
done
echo "MySQL is ready!"

# Install WordPress test suite if not already installed
if [ ! -f "$WP_TESTS_DIR/includes/functions.php" ]; then
    echo "Installing WordPress test suite..."

    # Create directories
    mkdir -p "$WP_TESTS_DIR"
    mkdir -p "$WP_CORE_DIR"

    # Download WordPress
    echo "Downloading WordPress..."
    curl -s https://wordpress.org/latest.tar.gz | tar xz -C "$WP_CORE_DIR" --strip-components=1

    # Download test suite
    echo "Downloading test suite..."
    svn export --quiet --ignore-externals https://develop.svn.wordpress.org/tags/6.7/tests/phpunit/includes/ "$WP_TESTS_DIR/includes"
    svn export --quiet --ignore-externals https://develop.svn.wordpress.org/tags/6.7/tests/phpunit/data/ "$WP_TESTS_DIR/data"

    # Download wp-tests-config.php
    curl -s https://develop.svn.wordpress.org/tags/6.7/wp-tests-config-sample.php > "$WP_TESTS_DIR/wp-tests-config.php"

    # Configure wp-tests-config.php
    sed -i "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s:__DIR__ . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/youremptytestdbnamehere/$WORDPRESS_DB_NAME/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourusernamehere/$WORDPRESS_DB_USER/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourpasswordhere/$WORDPRESS_DB_PASSWORD/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s|localhost|$WORDPRESS_DB_HOST|" "$WP_TESTS_DIR/wp-tests-config.php"

    echo "WordPress test suite installed!"
fi

# Create database if it doesn't exist
mysql -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $WORDPRESS_DB_NAME;" 2>/dev/null || true

# Run the command
exec "$@"
