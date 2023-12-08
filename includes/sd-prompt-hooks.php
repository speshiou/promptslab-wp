<?php

function pl_sd_prompt_content( $content ) {
    global $post;

    if ( !is_sd_prompt( $post ) ) {
        return $content;
    }

    $cat_sd_prompt = get_term_by('slug', 'sd-prompt', 'category');
    $args = [
        'parent' => $cat_sd_prompt->term_id,
        'hide_empty' => false,
    ];
    $categories = get_categories( $args );
    $category_map = array_reduce($categories, function($carry, $item) {
        $carry[$item->slug] = $item;
        return $carry;
    }, []);

    $content = sd_prompt_content($post, $content);

    // replace category links
    $content = preg_replace_callback("/💡 concept/i", function($matches) use ($category_map) {
        $link = array_key_exists('concept', $category_map) ? get_category_link($category_map['concept']) : '';
        return sprintf('💡 <a href="%s">concept</a>', esc_url($link));
    }, $content);

    // replace category links
    $content = preg_replace_callback("/([^<>]+) (-|\&#8211;) ([^<>]+)/i", function($matches) use ($category_map) {
        $slug = $matches[1];
        $link = array_key_exists($slug, $category_map) ? get_category_link($category_map[$slug]) : '';
        return sprintf('<a href="%s">%s</a> &#8211; %s', $link, $slug, $matches[3]);
    }, $content);

    return $content;
}

add_filter( 'the_content', 'pl_sd_prompt_content' );