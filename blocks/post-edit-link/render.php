<?php
    $postId = isset($block->context['postId']) ? $block->context['postId']: null;
    $wrapper_attributes = get_block_wrapper_attributes();
?>
<?php if ($postId && current_user_can( 'edit_post', $postId )):?>
<p <?php echo $wrapper_attributes; ?>>
    <?php echo edit_post_link(text: 'Edit', post: $postId); ?>
</p>
<?php endif; ?>