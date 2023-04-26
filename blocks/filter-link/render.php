<?php
    if (isset($attributes['category']) && isset($attributes['tag']) && $attributes['category'] && $attributes['tag']) {
        $cat_ID = get_cat_ID($attributes['category']);
        $link = get_category_link($cat_ID);
        $filter_link = add_query_arg('filter', $attributes['tag'], $link);

        $tag = get_term_by('slug', $attributes['tag'], 'post_tag');
        $link_text = sprintf('See more %s prompts', $tag->name);
    }

    $wrapper_attributes = get_block_wrapper_attributes();
?>
<?php if (isset($filter_link)):?>
<p <?php echo $wrapper_attributes; ?>>
    <a href="<?php echo esc_url($filter_link); ?>"><?php esc_html_e($link_text); ?></a>
</p>
<?php else: ?>
<p>Please select a category and a tag to generate a filter link</p>
<?php endif; ?>