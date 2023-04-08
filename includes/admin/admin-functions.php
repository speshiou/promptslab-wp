<?php
function pl_admin_register_setting($page_id, $sections, $option_key) {
    // Register a new setting for admin menu page.
	register_setting( $page_id, $option_key );

    $options = get_option( $option_key, [] );

    foreach ($sections as $section) {
        $section_id = $section['id'];
        add_settings_section(
            $section_id,
            $section['title'], 
            null,
            $page_id
        );

        foreach ($section['fields'] as $field) {
            $field_id = $field['id'];
            $args = $field;
            if (isset($field['args'])) {
                $args = array_merge($args, $field['args']);
            }
            $args['name'] = sprintf("%s[%s]", PL_OPTIONS_KEY, $field_id);
            if (!isset($args['value']) || !$args['value']) {
                $args['value'] = isset($options[$field_id]) ? $options[$field_id] : null;
            }
            add_settings_field(
                $field_id, // As of WP 4.6 this value is used only internally.
                                        // Use $args' label_for to populate the id inside the callback.
                $field['title'],
                pl_option_field_callback($field['type']),
                $page_id,
                $section_id,
                $args,
            );
        }
    }
}

/**
 * Field callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */

 function pl_field_text_input( $args ) {
    ?>
    <input name="<?php echo esc_attr($args['name']); ?>" type="text" id="<?php echo esc_attr($args['id']); ?>" value="<?php echo esc_attr($args['value']); ?>" class="regular-text">
    <?php
}

function pl_field_link( $args ) {
    ?>
    <a href="<?php echo esc_url($args['url']); ?>"><?php esc_html_e($args['text']) ?></a>
    <?php
}

function pl_field_plain_text( $args ) {
    ?>
    <p><?php esc_html_e($args['text']) ?></p>
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

function pl_option_field_callback($type) {
    switch ($type) {
        case 'text':
            return 'pl_field_text_input';
        case 'link':
            return 'pl_field_link';
        case 'plain_text':
            return 'pl_field_plain_text';
        default:
            return null;
    }
}

function pl_admin_setting_page($page_id, $msg_group_id) {
    // show error/update messages
	settings_errors( $msg_group_id );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting admin menu
			settings_fields( $page_id );
			// output setting sections and their fields
			// (sections are registered for admin menu, each field is registered to a specific section)
			do_settings_sections( $page_id );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}