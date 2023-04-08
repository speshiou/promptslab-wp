<?php

add_action( 'wp_after_insert_post', 'send_to_twitter', 10, 4 );

function send_to_twitter( $post_id, $post, $update, $post_before ) {
    // Check if post is published or updated
    if ( $post->post_status == 'publish' || $update ) {

        $category_slug = 'sd-prompt';
        $category = get_category_by_slug( $category_slug );
        if ( !$category || !has_category( $category->term_id, $post_id ) ) {
            return;
        }
        
        // Get the post content
        $content = $post->post_content;
        // Replace new lines to markdown format
        $content = str_replace("<br>", "\n", $content);
        $plain_text_content = wp_strip_all_tags( $content );
        // add inline code formatting to the prompt
        $plain_text_content = preg_replace_callback("/💡 (.*)/i", function($matches) {
            return sprintf("💡 `%s`", $matches[1]);
        }, $plain_text_content);

        $trailing_hashtags = pl_option(PL_OPTION_TWITTER_TRAILING_HASHTAGS);
        if ($trailing_hashtags) {
            $plain_text_content .= "\n\n" . $trailing_hashtags;
        }

        $image_paths = [];
        
        // Get all images from post content and keep display orders
        $image_ids = parse_image_ids_from_content($content);
        foreach ( $image_ids as $image_id ) {
            $filename = get_attached_file($image_id);
            // Use the attachment URL to send image to Telegram
            $image_paths[] = $filename;
        }

        if (empty($image_paths)) {
            return;
        }
        
        // Send message and images to Twitter
        $twitter_api = new TwitterAPI();

        $tweet_id = null;
        if ($update) {
            $tweet_id = get_post_meta( $post_id, 'twitter_post_id', true );
            if ($tweet_id) {
                $twitter_api->delete_tweet($tweet_id);
                $tweet_id = null;
            }
        }

        if ( !$tweet_id ) {
            $tweet_id = $twitter_api->post_tweet_with_images($plain_text_content, $image_paths);
            if ($tweet_id) {
                update_post_meta( $post_id, 'twitter_post_id', $tweet_id);
            }
        }
    }
}
