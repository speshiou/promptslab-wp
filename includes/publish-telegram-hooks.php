<?php

add_action( 'wp_after_insert_post', 'send_to_telegram', 10, 4 );

function send_to_telegram( $post_id, $post, $update, $post_before ) {
    // Check if post is published or updated
    if ( $post->post_status == 'publish' || $update ) {
        
        // Get the post content
        $content = $post->post_content;
        // Replace new lines to markdown format
        $content = str_replace("<br>", "\n", $content);
        $plain_text_content = wp_strip_all_tags( $content );
        // add inline code formatting to the prompt
        $plain_text_content = preg_replace_callback("/💡 (.*)/i", function($matches) {
            return sprintf("💡 `%s`", $matches[1]);
        }, $plain_text_content);
          

        // get the post tags
        $tags = wp_get_post_tags( $post_id );

        if ( $tags ) { // If tags exist for the post
            $hashtags = [];

            foreach( $tags as $tag ) { // Loop through each tag object
                $hashtags[] = '#' . $tag->slug; // Retrieves the slug of the tag object
            }

            if (!empty($hashtags)) {
                $plain_text_content .= "\n\n" . implode(" ", $hashtags);
            }
        }
        
        
        // Get the post featured image if it exists
        if ( has_post_thumbnail( $post_id ) ) {
            $thumb_id = get_post_thumbnail_id( $post_id );
            $attachment = wp_get_attachment_image_src( $thumb_id, 'full' );
      // Use the attachment URL to send image to Telegram
        }

        $image_urls = [];
        
        // Get all images attached to the post
        $attachments = get_attached_media( 'image', $post_id );
        foreach ( $attachments as $attachment ) {
            $image_url = $attachment->guid;
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
