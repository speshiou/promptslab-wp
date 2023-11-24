<?php

function pl_sd_prompt_content( $content ) {
    global $post;

    if ( !in_category( 'sd-prompt', $post ) ) {
        return $content;
    }

    $category = get_term_by('slug', 'sd-prompt', 'category');
    $cat_link = get_category_link($category->term_id);

    $content = sd_prompt_content($post, $content);

    // add concept filter link
    $content = preg_replace_callback("/💡 concept/i", function($matches) use ($cat_link) {
        $link = add_query_arg( 'filter', 'concept', $cat_link );
        return sprintf('💡 <a href="%s">concept</a>', esc_url($link));
    }, $content);

    // add tag filter links
    $content = preg_replace_callback("/([^<>]+) (-|\&#8211;) ([^<>]+)/i", function($matches) use ($cat_link) {
        $link = add_query_arg( 'filter', $matches[1], $cat_link );
        return sprintf('<a href="%s">%s</a> &#8211; %s', $link, $matches[1], $matches[3]);
    }, $content);

    return $content;
}

add_filter( 'the_content', 'pl_sd_prompt_content' );