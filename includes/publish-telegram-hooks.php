<?php

add_action( 'wp_after_insert_post', 'send_to_telegram', 10, 4 );

function send_to_telegram( $post_id, $post, $update, $post_before ) {
    // Check if post is published or updated
    if ( $post->post_status == 'publish' ) {
        if ( !is_sd_prompt($post) ) {
            return;
        }
        
        // Get the post content
        $content = sd_prompt_content($post);
        // Replace new lines to markdown format
        $content = str_replace("<br>", "\n", $content);
        $plain_text_content = wp_strip_all_tags( $content );
        $plain_text_content = trim($plain_text_content);
        // add inline code formatting to the prompt
        $plain_text_content = preg_replace_callback("/💡 (.*)/i", function($matches) {
            if ($matches[1] == 'concept') {
                return sprintf("💡 #%s", $matches[1]);
            }
            return sprintf("💡 <code>%s</code>", $matches[1]);
        }, $plain_text_content);
        // concept attributes
        $plain_text_content = preg_replace_callback("/(.*) \- (.*)/i", function($matches) {
            return sprintf("#%s - <code>%s</code>", $matches[1], $matches[2]);
        }, $plain_text_content);

        // get the post categories
        $categories = wp_get_post_categories($post_id, [ 'fields' => 'all']);

        if ( $categories ) { // If terms exist for the post
            $hashtags = [];

            foreach( $categories as $cat ) { // Loop through each term object
                $hashtags[] = '#' . $cat->slug; // Retrieves the slug of the term object
            }

            if (!empty($hashtags)) {
                $hashtags_str = implode(" ", $hashtags);
                if (str_endswith_hashtag($plain_text_content)) {
                    $plain_text_content .= " " . $hashtags_str;
                } else {
                    $plain_text_content .= "\n\n" . $hashtags_str;
                }
            }
        }

        $image_urls = [];
        
        // Get all images from post content and keep display orders
        $image_ids = parse_image_ids_from_content($content);
        foreach ( $image_ids as $image_id ) {
            $image_url = wp_get_attachment_url($image_id);
            // Use the attachment URL to send image to Telegram
            $image_urls[] = $image_url;
        }

        if (empty($image_urls)) {
            return;
        }
        
        // Send message and images to Telegram
        $chat_id = pl_option(PL_OPTION_SD_CHANNEL_CHAT_ID);
        $telegram_api = new TelegramAPI();

        $message_id = null;
        if ($update) {
            $message_id = get_post_meta( $post_id, 'tg_msg_id', true );
        }

        if ( !$message_id ) {
            $message_id = $telegram_api->send_message_with_photos($chat_id, $plain_text_content, $image_urls);
            if ($message_id) {
                update_post_meta( $post_id, 'tg_msg_id', $message_id);
            }
        } else {
            $telegram_api->edit_message_caption($chat_id, $message_id, $plain_text_content);
        }
    }
}
