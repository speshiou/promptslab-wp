<?php if (is_category()):
$wrapper_attributes = get_block_wrapper_attributes();
$active_color_slug = isset($attributes['active_color']) ? $attributes['active_color']['slug'] : null;
$current_category = get_queried_object();
$root_category = get_top_level_category($current_category->term_id);
$root_category_permalink = get_category_link($root_category);
$args = [
    'parent' => $root_category->term_id,
    'hide_empty' => false,
];
$categories = get_categories( $args );
?>
<nav <?php echo $wrapper_attributes; ?>>
    <ul>
        <?php foreach ($categories as $category): 
            $active = $current_category->term_id == $category->term_id;
            $item_style = $active ? 'style="background-color: var(' . theme_color_var($active_color_slug) . ');"' : '';
            $category_permalink = $active ? $root_category_permalink : get_category_link($category);
        ?>
            <li class="<?php echo $active ? 'active' : ''; ?>" <?php echo $item_style ?>>
                <a href="<?php echo esc_url($category_permalink); ?>">
                    <?php echo $category->name; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php endif; ?>