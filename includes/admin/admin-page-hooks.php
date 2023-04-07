<?php

define( "PL_ADMIN_PAGE_ID", "promptslab" );
define( "PL_OPTION_TELEGRAM_BOT_API_KEY", "telegram_bot_api_key" );
define( "PL_OPTION_SD_CHANNEL_CHAT_ID", "sd_channel_chat_id" );

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function pl_settings_init() {
	// Register a new setting for admin menu page.
	register_setting( PL_ADMIN_PAGE_ID, PL_OPTIONS_KEY );

    $sections = [
        [
            'id' => 'pl_section_telegram',
            'title' => __( 'Telegram' ),
            'fields' => [
                [
                    'id' => PL_OPTION_TELEGRAM_BOT_API_KEY,
                    'title' => __( 'Telegram bot API key' ),
                    'type' => 'text',
                ],
                [
                    'id' => PL_OPTION_SD_CHANNEL_CHAT_ID,
                    'title' => __( 'SD Channel Chat ID' ),
                    'type' => 'text',
                ],
            ]
        ]
    ];

    $options = get_option( PL_OPTIONS_KEY, [] );

    foreach ($sections as $section) {
        $section_id = $section['id'];
        add_settings_section(
            $section_id,
            $section['title'], 
            null,
            PL_ADMIN_PAGE_ID
        );

        foreach ($section['fields'] as $field) {
            $field_id = $field['id'];
            $args = $field;
            if (isset($field['args'])) {
                $args = array_merge($args, $field['args']);
            }
            $args['name'] = sprintf("%s[%s]", PL_OPTIONS_KEY, $field_id);
            $args['value'] = isset($options[$field_id]) ? $options[$field_id] : null;
            add_settings_field(
                $field_id, // As of WP 4.6 this value is used only internally.
                                        // Use $args' label_for to populate the id inside the callback.
                $field['title'],
                pl_option_field_callback($field['type']),
                PL_ADMIN_PAGE_ID,
                $section_id,
                $args,
            );
        }
    }

	// Register a new field in the "pl_section_telegram" section, inside the admin menu page.
	// add_settings_field(
	// 	'wporg_field_pill', // As of WP 4.6 this value is used only internally.
	// 	                        // Use $args' label_for to populate the id inside the callback.
	// 	__( 'Pill' ),
	// 	'pl_field_pill_cb',
	// 	PL_ADMIN_PAGE_ID,
	// 	'pl_section_telegram',
	// 	array(
	// 		'label_for'         => 'wporg_field_pill',
	// 		'class'             => 'wporg_row',
	// 		'wporg_custom_data' => 'custom',
	// 	)
	// );
}

/**
 * Register our pl_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'pl_settings_init' );


/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function pl_section_telegram_callback( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.' ); ?></p>
	<?php
}

function pl_field_text_input( $args ) {
    ?>
    <input name="<?php echo esc_attr($args['name']); ?>" type="text" id="<?php echo esc_attr($args['id']); ?>" value="<?php echo esc_attr($args['value']); ?>" class="regular-text">
    <?php
}

/**
 * Pill field callbakc function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function pl_field_pill_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( PL_OPTIONS_KEY );
	?>
	<select
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			data-custom="<?php echo esc_attr( $args['wporg_custom_data'] ); ?>"
			name="pl_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
		<option value="red" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'red', false ) ) : ( '' ); ?>>
			<?php esc_html_e( 'red pill' ); ?>
		</option>
 		<option value="blue" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'blue', false ) ) : ( '' ); ?>>
			<?php esc_html_e( 'blue pill' ); ?>
		</option>
	</select>
	<p class="description">
		<?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.' ); ?>
	</p>
	<?php
}

/**
 * Add the top level menu page.
 */
function pl_options_page() {
	add_menu_page(
		'Prompts Lab Settings',
		'Prompts Lab',
		'manage_options',
		'pl-admin',
		'pl_options_page_html'
	);
}


/**
 * Register our options page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'pl_options_page' );


/**
 * Top level menu callback function
 */
function pl_options_page_html() {
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

	// show error/update messages
	settings_errors( 'pl_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting admin menu
			settings_fields( PL_ADMIN_PAGE_ID );
			// output setting sections and their fields
			// (sections are registered for admin menu, each field is registered to a specific section)
			do_settings_sections( PL_ADMIN_PAGE_ID );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}

function pl_option_field_callback($type) {
    switch ($type) {
        case 'text':
            return 'pl_field_text_input';
        default:
            return null;
    }
}