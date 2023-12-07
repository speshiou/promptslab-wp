<?php
function pl_load_base_template($template, $type, $templates)
{    
    $category = get_queried_object();
    $root_category = get_top_level_category($category->term_id);

    $root_templates = [
        $type . '-' . $root_category->slug . '.php',
        $type . '-' . $root_category->term_id . '.php',
        $type . '.php',
    ];

    $block_template = locate_block_template( $template, $type, $root_templates );

    return $block_template ? $block_template : $template;
}

add_filter('category_template', 'pl_load_base_template', 10, 3);