<?php
    $postId = isset($block->context['postId']) ? $block->context['postId']: null;
    if ($postId) {
        $content = get_the_content(post: $postId);
        $content = apply_filters( 'the_content', str_replace( ']]>', ']]&gt;', $content ) );
    }
    $wrapper_attributes = get_block_wrapper_attributes();
?>
<?php if ( isset($content) ):?>
<div <?php echo $wrapper_attributes; ?>>
    <?php echo $content; ?>       
</div>
<?php endif; ?>