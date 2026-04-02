#!/bin/bash

# =============================================
# EzExam - Build Distribution Package
# =============================================
# Creates a clean, shareable zip file without
# secrets, databases, or development files.
# =============================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

VERSION="1.2.0"
DIST_NAME="EzExam-v${VERSION}"
DIST_DIR="dist/${DIST_NAME}"
ZIP_FILE="dist/${DIST_NAME}.zip"

echo ""
echo -e "${BLUE}==============================================${NC}"
echo -e "${BLUE}   EzExam - Build Distribution Package${NC}"
echo -e "${BLUE}==============================================${NC}"
echo ""

# Clean previous builds
echo -e "${YELLOW}[1/5] Cleaning previous builds...${NC}"
rm -rf dist/
mkdir -p "${DIST_DIR}"

# Install production dependencies
echo -e "${YELLOW}[2/5] Installing production dependencies...${NC}"
composer install --optimize-autoloader --no-dev --quiet
npm install --silent

# Build frontend assets
echo -e "${YELLOW}[3/5] Building frontend assets...${NC}"
npm run build

# Copy files (excluding secrets and dev files)
echo -e "${YELLOW}[4/5] Packaging files...${NC}"
rsync -a --quiet \
    --exclude='dist/' \
    --exclude='.git/' \
    --exclude='.claude/' \
    --exclude='.env' \
    --exclude='.env.backup' \
    --exclude='.env.production' \
    --exclude='node_modules/' \
    --exclude='vendor/' \
    --exclude='database/database.sqlite' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='storage/.installed' \
    --exclude='storage/app/public/*' \
    --exclude='tests/' \
    --exclude='build.sh' \
    --exclude='.phpunit.result.cache' \
    --exclude='phpunit.xml' \
    ./ "${DIST_DIR}/"

# Include vendor (production only)
echo -e "  Copying vendor dependencies..."
composer install --optimize-autoloader --no-dev --quiet
rsync -a --quiet vendor/ "${DIST_DIR}/vendor/"

# Ensure required empty directories exist with .gitkeep
mkdir -p "${DIST_DIR}/storage/app/public"
mkdir -p "${DIST_DIR}/storage/framework/cache/data"
mkdir -p "${DIST_DIR}/storage/framework/sessions"
mkdir -p "${DIST_DIR}/storage/framework/views"
mkdir -p "${DIST_DIR}/storage/logs"
mkdir -p "${DIST_DIR}/database"
touch "${DIST_DIR}/storage/app/.gitkeep"
touch "${DIST_DIR}/storage/app/public/.gitkeep"
touch "${DIST_DIR}/storage/framework/cache/data/.gitkeep"
touch "${DIST_DIR}/storage/framework/sessions/.gitkeep"
touch "${DIST_DIR}/storage/framework/views/.gitkeep"
touch "${DIST_DIR}/storage/logs/.gitkeep"

# Create zip
echo -e "${YELLOW}[5/5] Creating zip archive...${NC}"
cd dist/
zip -r -q "${DIST_NAME}.zip" "${DIST_NAME}/"
cd ..

# Summary
ZIP_SIZE=$(du -h "${ZIP_FILE}" | cut -f1)
echo ""
echo -e "${GREEN}==============================================${NC}"
echo -e "${GREEN}   Build Complete!${NC}"
echo -e "${GREEN}==============================================${NC}"
echo ""
echo -e "  ${BLUE}Package:${NC}  ${ZIP_FILE}"
echo -e "  ${BLUE}Size:${NC}     ${ZIP_SIZE}"
echo ""
echo -e "  ${BLUE}To install:${NC}"
echo -e "  1. Upload & extract the zip on your server"
echo -e "  2. Visit https://yourdomain.com/install.php"
echo ""
