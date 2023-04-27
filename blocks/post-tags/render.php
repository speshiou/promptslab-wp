<?php
    $postId = isset($block->context['postId']) ? $block->context['postId']: null;
    if ($postId) {
        $categories = get_the_category($postId);
        if ($categories && !empty($categories)) {
            $cat_link = get_category_link($categories[0]->term_id);
            $tags = get_the_tags($postId);
        }
    }
    $wrapper_attributes = get_block_wrapper_attributes();
?>
<?php if ( isset($cat_link) && isset($tags) && is_array($tags) ):?>
<div <?php echo $wrapper_attributes; ?>>
    <?php foreach ($tags as $tag):?>
        <a href="<?php echo esc_url(add_query_arg('filter', $tag->slug, $cat_link)); ?>"><?php esc_html_e($tag->name); ?></a>
    <?php endforeach; ?>        
</div>
<?php endif; ?>