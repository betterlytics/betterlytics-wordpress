#!/bin/bash

# Exit on error
set -e

# Configuration
PLUGIN_SLUG="betterlytics"
BUILD_DIR="${PLUGIN_SLUG}"
ZIP_FILE="${PLUGIN_SLUG}.zip"

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}Starting build process for ${PLUGIN_SLUG}...${NC}"

# Check for required commands
if ! command -v zip &> /dev/null; then
    echo -e "${RED}Error: 'zip' command not found. Please install zip or run this script in an environment that has it (e.g., Git Bash, WSL).${NC}"
    exit 1
fi

if ! command -v pnpm &> /dev/null; then
    echo -e "${RED}Error: 'pnpm' command not found. Please message the developer.${NC}"
    exit 1
fi

# 1. Cleanup previous builds
echo -e "${BLUE}Cleaning up previous builds...${NC}"
rm -rf "${BUILD_DIR}" "${ZIP_FILE}"

# 2. Build Assets
echo -e "${BLUE}Building assets...${NC}"
echo "Installing dependencies..."
pnpm install
echo "Compiling CSS..."
pnpm run build:css

# 3. Create Build Directory
echo -e "${BLUE}Creating build directory...${NC}"
mkdir -p "${BUILD_DIR}"

# 4. Copy Files
echo -e "${BLUE}Copying files...${NC}"

# Copy root files
cp betterlytics.php "${BUILD_DIR}/"
cp readme.txt "${BUILD_DIR}/"
cp LICENSE "${BUILD_DIR}/"
cp uninstall.php "${BUILD_DIR}/"
if [ -f "index.php" ]; then
    cp index.php "${BUILD_DIR}/"
fi

# Copy directories
echo "Copying admin directory..."
cp -r admin "${BUILD_DIR}/"

echo "Copying includes directory..."
cp -r includes "${BUILD_DIR}/"

echo "Copying public directory..."
cp -r public "${BUILD_DIR}/"

echo "Copying languages directory..."
if [ -d "languages" ]; then
    cp -r languages "${BUILD_DIR}/"
fi

# 5. Remove Development Files from Build Directory
echo -e "${BLUE}Removing development files from build...${NC}"
# Remove map files if they exist
find "${BUILD_DIR}" -name "*.map" -type f -delete
# Remove any potential node_modules or vendor directories if they were copied
rm -rf "${BUILD_DIR}/admin/node_modules"
rm -rf "${BUILD_DIR}/includes/node_modules"
rm -rf "${BUILD_DIR}/public/node_modules"
# Remove source CSS (optional, based on preference, but keeping simple for now)
# find "${BUILD_DIR}" -name "*.src.css" -delete

# 6. Create Zip
echo -e "${BLUE}Creating zip archive...${NC}"
zip -r "${ZIP_FILE}" "${BUILD_DIR}" -q

# 7. Final Cleanup
echo -e "${BLUE}Cleaning up temporary files...${NC}"
rm -rf "${BUILD_DIR}"

echo -e "${GREEN}Build complete! Created ${ZIP_FILE}${NC}"
