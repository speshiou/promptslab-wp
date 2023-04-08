<?php

define( "PL_ADMIN_PAGE_ID", "promptslab" );
define( "PL_OPTION_TELEGRAM_BOT_API_KEY", "telegram_bot_api_key" );
define( "PL_OPTION_SD_CHANNEL_CHAT_ID", "sd_channel_chat_id" );
define("PL_OPTION_TWITTER_TRAILING_HASHTAGS", "trail_hashtags");
define("PL_OPTION_TWITTER_CONSUMER_KEY", "consumer_key");
define("PL_OPTION_TWITTER_CONSUMER_SECRET", "consumer_secret");
define("PL_OPTION_TWITTER_ACCESS_TOKEN", "access_token");
define("PL_OPTION_TWITTER_ACCESS_TOKEN_SECRET", "access_token_secret");
define("PL_OPTION_TWITTER_CLIENT_ID", "twitter_client_id");
define("PL_OPTION_TWITTER_CLIENT_SECRET", "twitter_client_secret");
define("PL_OPTION_TWITTER_AUTH_CODE", "twitter_auth_code");

class PL_OptionSetting extends PL_AdminPage {
    public function __construct() {
        parent::__construct(
            'Prompts Lab Settings',
            'Prompts Lab',
            'pl-admin',
        );
	}

    function settings_init() {
        $sections = [
            [
                'id' => 'pl_section_telegram',
                'title' => __( 'Telegram' ),
                'fields' => [
                    [
                        'id' => PL_OPTION_TELEGRAM_BOT_API_KEY,
                        'title' => __( 'bot API key' ),
                        'type' => 'text',
                    ],
                    [
                        'id' => PL_OPTION_SD_CHANNEL_CHAT_ID,
                        'title' => __( 'SD Channel Chat ID' ),
                        'type' => 'text',
                    ],
                ]
            ],
            [
                'id' => 'pl_section_twitter',
                'title' => __( 'Twitter' ),
                'fields' => [
                    [
                        'id' => PL_OPTION_TWITTER_TRAILING_HASHTAGS,
                        'title' => __( 'trailing hashtags' ),
                        'type' => 'text',
                    ],
                    [
                        'id' => PL_OPTION_TWITTER_CONSUMER_KEY,
                        'title' => __( 'consumer key' ),
                        'type' => 'text',
                    ],
                    [
                        'id' => PL_OPTION_TWITTER_CONSUMER_SECRET,
                        'title' => __( 'consumer secret' ),
                        'type' => 'text',
                    ],
                    [
                        'id' => PL_OPTION_TWITTER_ACCESS_TOKEN,
                        'title' => __( 'access token' ),
                        'type' => 'text',
                    ],
                    [
                        'id' => PL_OPTION_TWITTER_ACCESS_TOKEN_SECRET,
                        'title' => __( 'access token secret' ),
                        'type' => 'text',
                    ],
                    [
                        'id' => PL_OPTION_TWITTER_CLIENT_ID,
                        'title' => __( 'Client ID' ),
                        'type' => 'text',
                    ],
                    [
                        'id' => PL_OPTION_TWITTER_CLIENT_SECRET,
                        'title' => __( 'Client Secret' ),
                        'type' => 'text',
                    ],
                    [
                        'id' => 'auth_url',
                        'title' => __( 'Auth Url' ),
                        'type' => 'link',
                        'args' => [
                            'url' => (new TwitterAPI())->auth_url(),
                            'text' => 'Get auth code',
                        ]
                    ],
                    [
                        'id' => PL_OPTION_TWITTER_AUTH_CODE,
                        'title' => __( 'Auth code' ),
                        'type' => 'text',
                        'value' => isset($_GET['code']) ? $_GET['code'] : null,
                    ],
                ]
            ],
        ];
    
        pl_admin_register_setting(PL_ADMIN_PAGE_ID, $sections, PL_OPTIONS_KEY);
    }

    function options_page_html() {
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // add error/update messages

        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'pl_messages', 'pl_message', __( 'Settings Saved' ), 'updated' );
        }

        // check if callback from twitter
        if ( isset( $_GET['code'] ) ) {
            // prompt users to save settings
            add_settings_error( 'pl_messages', 'pl_message', __( 'Twitter Auth Code updated, please save settings to persist new value' ), 'updated' );
        }

        pl_admin_setting_page(PL_ADMIN_PAGE_ID, 'pl_messages');
    }
}

new PL_OptionSetting();