<?php
    $selected_tags = isset($attributes['tags']) && is_array($attributes['tags']) ? $attributes['tags'] : [];
    $cache = array_reduce($selected_tags, function($carry, $entity) {
        $carry[$entity] = true;
        return $carry;
    }, []);
    $tags = get_tags(['hide_empty' => false]);
    $filters = [];
    foreach ($tags as $tag) {
        if (array_key_exists($tag->slug, $cache)) {
            $filters[] = $tag;
        }
    }

    $wrapper_attributes = get_block_wrapper_attributes();
?>
<nav <?php echo $wrapper_attributes; ?>>
    <ul>
        <?php foreach ($filters as $tag): ?>
            <li class="<?php echo get_query_var('filter') == $tag->slug ? 'active' : ''; ?>"><a href="<?php echo esc_url(add_query_arg( 'filter', $tag->slug )); ?>"><?php echo $tag->name; ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>