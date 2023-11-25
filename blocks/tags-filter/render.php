<?php
    $selected_tags = isset($attributes['tags']) && is_array($attributes['tags']) ? $attributes['tags'] : [];
    $tags = get_tags(['hide_empty' => false]);
    $cache = array_reduce($tags, function($carry, $tag) {
        $carry[$tag->slug] = $tag;
        return $carry;
    }, []);
    
    $filters = [];
    foreach ($selected_tags as $slug) {
        if (array_key_exists($slug, $cache)) {
            $filters[] = $cache[$slug];
        }
    }

    $active_color_slug = isset($attributes['active_color']) ? $attributes['active_color']['slug'] : null;

    $wrapper_attributes = get_block_wrapper_attributes();
?>
<nav <?php echo $wrapper_attributes; ?>>
    <ul>
        <?php foreach ($filters as $tag): 
            $active = get_query_var('filter') == $tag->slug;
            $item_style = $active ? 'style="background-color: var(' . theme_color_var($active_color_slug) . ');"' : '';
        ?>
            <li class="<?php echo $active ? 'active' : ''; ?>" <?php echo $item_style ?>>
                <a href="<?php echo esc_url(add_query_arg( 'filter', $tag->slug )); ?>">
                    <?php echo $tag->name; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>