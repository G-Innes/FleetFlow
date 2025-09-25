#!/bin/bash
# Build script for FleetFlow
echo "Building assets..."
npm run build
echo "Fixing manifest location..."
cp public/build/.vite/manifest.json public/build/manifest.json
echo "Build complete!"


