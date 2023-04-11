<?php

class TelegramAPI {

    private $token;

    public function __construct() {
		$this->token = pl_option(PL_OPTION_TELEGRAM_BOT_API_KEY);
	}

    public function get_recent_chats() {
        $chats = [];
        $url = "https://api.telegram.org/bot{$this->token}/getUpdates";
        $response = wp_remote_get( $url );
        if ( is_wp_error( $response ) ) {
            // Handle error
        } else {
            $result = json_decode( $response['body'] );
            foreach ($result->result as $update) {
                $chat = null;
                if ( isset($update->message) ) {
                    $chat = $update->message->chat;
                } else if ( isset($update->channel_post) ) {
                    $chat = $update->channel_post->chat;
                }

                if (!$chat) continue;
                
                $chats[] = [
                    'id' => $chat->id,
                    'title' => $chat->title,
                ];
            }
            
        }
        return $chats;
    }
    
    // Sends a message with photos to the specified chat ID
    public function send_message_with_photos( $chat_id, $message, $photo_urls, $photo_caption = null) {
        $url = "https://api.telegram.org/bot{$this->token}/sendMediaGroup";
        $media_array = array();
        foreach ( $photo_urls as $i => $photo_url ) {
            $media = array(
                'type' => 'photo',
                'media' => $photo_url,
            );

            if ($i == 0) {
                // trick to show message below a media group
                $media['caption'] = $message;
                $media['parse_mode'] = 'HTML';
            }

            $media_array[] = $media;
        }
        $params = array(
            'chat_id' => $chat_id,
            'media' => json_encode( $media_array ),
        );

        $response = wp_remote_post( $url, array( 'body' => $params ) );
        if ( is_wp_error( $response ) ) {
            error_log(sprintf("Telegram error %s: %s", $response->get_error_code(), $response->get_error_message()));
            return false;
        } else {
            $result = json_decode( $response['body'] );
            if ( $result->ok ) {
                return $result->result[0]->message_id;
            } else {
                error_log($response['body']);
                return false;
            }
        }
    }
    
    
    // Edits a message in the specified chat ID and message ID
    public function edit_message( $chat_id, $message_id, $edited_message ) {
        $url = "https://api.telegram.org/bot{$this->token}/editMessageText";
        $params = array(
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $edited_message,
            'parse_mode' => 'HTML'
        );
        $response = wp_remote_post( $url, array( 'body' => $params ) );
        if ( is_wp_error( $response ) ) {
            error_log(sprintf("Telegram error %s: %s", $response->get_error_code(), $response->get_error_message()));
            return false;
        } else {
            $result = json_decode( $response['body'] );
            if ( $result->ok ) {
                return true;
            } else {
                error_log($response['body']);
                return false;
            }
        }
    }

    // Edits a message caption in the specified chat ID and message ID
    public function edit_message_caption( $chat_id, $message_id, $caption ) {
        $url = "https://api.telegram.org/bot{$this->token}/editMessageCaption";
        $params = array(
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'caption' => $caption,
            'parse_mode' => 'HTML'
        );
        $response = wp_remote_post( $url, array( 'body' => $params ) );
        if ( is_wp_error( $response ) ) {
            error_log(sprintf("Telegram error %s: %s", $response->get_error_code(), $response->get_error_message()));
            return false;
        } else {
            $result = json_decode( $response['body'] );
            if ( $result->ok ) {
                return true;
            } else {
                error_log($response['body']);
                return false;
            }
        }
    }
}
