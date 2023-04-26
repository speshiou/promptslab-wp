#!/bin/bash

# Exit if any command fails.
set -e

# Change to the expected directory.
cd "$(dirname "$0")"
cd ..

# clean up
rm -r build

echo "Generating build... 👷‍♀️"
npm run build

build_files=$(
	ls build/blocks/*/*.{js,css,php,json} \
)

# Generate the plugin zip file.
echo "Creating archive... 🎁"
zip -r promptslab-wp.zip \
	plugin.php \
	includes \
	vendor \
	$build_files \
	README.md

echo "Done. You've built plugin zip file! 🎉 "