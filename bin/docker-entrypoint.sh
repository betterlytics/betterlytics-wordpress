#!/bin/bash
set -e

WP_TESTS_DIR=${WP_TESTS_DIR:-/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR:-/tmp/wordpress}

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
while ! mysqladmin ping -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" --skip-ssl --silent; do
    sleep 1
done
echo "MySQL is ready!"

# Install WordPress test suite if not already installed
# Check if test suite exists
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

    # Download test suite from GitHub mirror (more reliable than SVN)
    echo "[2/4] Downloading test suite from GitHub..."
    WP_DEVELOP_DIR=$(mktemp -d)
    git clone --depth=1 --branch=7.0 https://github.com/WordPress/wordpress-develop.git "$WP_DEVELOP_DIR"
    cp -r "$WP_DEVELOP_DIR/tests/phpunit/includes" "$WP_TESTS_DIR/includes"
    cp -r "$WP_DEVELOP_DIR/tests/phpunit/data" "$WP_TESTS_DIR/data"
    cp "$WP_DEVELOP_DIR/wp-tests-config-sample.php" "$WP_TESTS_DIR/wp-tests-config.php"
    rm -rf "$WP_DEVELOP_DIR"
    echo "      Done!"

    # Configure wp-tests-config.php
    echo "[3/4] Configuring test environment..."
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
elif [ ! -f "$WP_CORE_DIR/wp-settings.php" ]; then
    echo "=========================================="
    echo "Restoring WordPress Core..."
    echo "=========================================="
    mkdir -p "$WP_CORE_DIR"
    curl --progress-bar https://wordpress.org/latest.tar.gz | tar xz -C "$WP_CORE_DIR" --strip-components=1
    echo "WordPress Core restored!"
else
    echo "Test suite and WordPress Core already installed."
fi

# Create database if it doesn't exist
mysql -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" --skip-ssl -e "CREATE DATABASE IF NOT EXISTS $WORDPRESS_DB_NAME;" 2>/dev/null || true

echo ""
echo "Running PHPUnit..."
echo ""

# Run the command
exec "$@"
