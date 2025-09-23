#!/bin/bash

# Create necessary Laravel directories for Vercel deployment
echo "Creating Laravel directories..."

# Create bootstrap cache directory
mkdir -p bootstrap/cache

# Create storage directories
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions
chmod -R 755 bootstrap/cache
chmod -R 755 storage

echo "Laravel directories created successfully!"
