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
    echo "=========================================="
    echo "Installing WordPress test suite..."
    echo "=========================================="

    # Create directories
    mkdir -p "$WP_TESTS_DIR"
    mkdir -p "$WP_CORE_DIR"

    # Download WordPress
    echo "[1/4] Downloading WordPress core..."
    curl --progress-bar https://wordpress.org/latest.tar.gz | tar xz -C "$WP_CORE_DIR" --strip-components=1
    echo "      Done!"

    # Download test suite
    echo "[2/4] Downloading test suite includes (this may take a minute)..."
    svn export --ignore-externals https://develop.svn.wordpress.org/tags/6.7/tests/phpunit/includes/ "$WP_TESTS_DIR/includes"
    echo "      Done!"

    echo "[3/4] Downloading test suite data..."
    svn export --ignore-externals https://develop.svn.wordpress.org/tags/6.7/tests/phpunit/data/ "$WP_TESTS_DIR/data"
    echo "      Done!"

    # Download wp-tests-config.php
    echo "[4/4] Configuring test environment..."
    curl -s https://develop.svn.wordpress.org/tags/6.7/wp-tests-config-sample.php > "$WP_TESTS_DIR/wp-tests-config.php"

    # Configure wp-tests-config.php
    sed -i "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s:__DIR__ . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/youremptytestdbnamehere/$WORDPRESS_DB_NAME/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourusernamehere/$WORDPRESS_DB_USER/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourpasswordhere/$WORDPRESS_DB_PASSWORD/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s|localhost|$WORDPRESS_DB_HOST|" "$WP_TESTS_DIR/wp-tests-config.php"
    echo "      Done!"

    echo "=========================================="
    echo "WordPress test suite installed!"
    echo "=========================================="
else
    echo "Test suite already cached, skipping download."
fi

# Create database if it doesn't exist
mysql -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $WORDPRESS_DB_NAME;" 2>/dev/null || true

echo ""
echo "Running PHPUnit..."
echo ""

# Run the command
exec "$@"
