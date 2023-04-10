<?php

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