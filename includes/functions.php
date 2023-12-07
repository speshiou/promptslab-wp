<?php

function pl_assets_url($filename) {
    return plugins_url( 'assets/' . $filename, PromptsLab::get_plugin_file() );
}

function pl_plugin_dir() {
    return dirname( PromptsLab::get_plugin_file() );
}

function pl_plugin_dir_filename($filename) {
    $ret = dirname( PromptsLab::get_plugin_file() );
    $ret = trailingslashit($ret) . $filename;
    return $ret;
}

function pl_option($key) {
    $options = get_option( PL_OPTIONS_KEY, [] );
    return isset($options[$key]) ? $options[$key] : null;
}

function find_image_blocks($blocks) {
    $images = [];
    foreach ($blocks as $block) {
        if ($block["blockName"] === "core/image") { // Block name of an image block.
            $images[] = $block;
        } else if (isset($block["innerBlocks"])) { // Check if the current block has inner blocks.
            $images = array_merge($images, find_image_blocks($block["innerBlocks"]));
        }
    }
    return $images;
}

function parse_image_ids_from_content($content) {
    $image_ids = array();
    $blocks = parse_blocks($content); // Using WP built-in function for parsing content to blocks.
    $images = find_image_blocks($blocks);
    foreach ($images as $image) {
        $image_ids[] = $image["attrs"]["id"]; // ID of the image.
    }
 
    return $image_ids;
}

function str_endswith_hashtag($text) {
    preg_match('/\#[^\s]+$/', $text, $match);
    return $match != null;
}

function pl_find_block_dirs($dir) {
    $block_json_dirs = array();
    $directories = new RecursiveDirectoryIterator($dir);
    foreach ($directories as $item) {
        if ($item->isDir() && $item->getFilename() !== '.' && $item->getFilename() !== '..') {
            $block_json_path = $item->getPathname() . DIRECTORY_SEPARATOR . 'block.json';
            if (file_exists($block_json_path)) {
                $block_json_dirs[] = $item->getPathname();
            }
        }
    }
    return $block_json_dirs;
}

function sd_prompt_content($post, $content = null) {
    if (!$content) {
        $content = $post->post_content;
    }
    if (!preg_match("/💡 (.*)/i", $content)) {
        $prompt = strtolower($post->post_title);
        $content .= "<p>💡 " . esc_html($prompt) . "</p>";
    }
    return $content;
}

function theme_color_var($slug) {
    return '--wp--preset--color--' . $slug;
}

function has_ancestor_category($cat_id, $ancestor_cat_slug) {
    $ancestor = get_category_by_slug( $ancestor_cat_slug );
    $ancestors = get_ancestors($cat_id, 'category');
    return in_array($ancestor->term_id, $ancestors);
}

function is_sd_prompt($post) {
    $categories = wp_get_post_categories($post->ID);
    return has_ancestor_category($categories[0], 'sd-prompt');
}

function get_top_level_category($category_id) {
    $category = get_category($category_id);
    
    // Check if the category has a parent
    if ($category->parent == 0) {
        // If the category has no parent, it is the top-level category
        return $category;
    } else {
        // If the category has a parent, recursively call the function to check the parent's parent
        return get_top_level_category($category->parent);
    }
}
