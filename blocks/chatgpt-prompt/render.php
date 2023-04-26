<?php
    $wrapper_attributes = get_block_wrapper_attributes(); 
    if ( isset($block->context['postId']) && in_category('chatgpt', $block->context['postId']) ) {
        $post = get_post($block->context['postId']);
        if ($post) {
            preg_match("/<pre[^>]+>\s*<code>(.*?)<\/code>\s*<\/pre>/", $post->post_content, $matches);
            if ($matches) {
                $prompt = trim($matches[1]);
            }
        }
    }
    
    
?>
<?php if ( isset($prompt) ):?>
<pre <?php echo $wrapper_attributes; ?>><code><?php echo $prompt; ?></code></pre>
<p><a href="<?php echo esc_url(get_permalink($post->ID)); ?>">See examples</a></p>
<?php endif; ?>