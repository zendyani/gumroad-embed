<?php
/**
 * Class for handling the Gumroad Embed settings page.
 */
class Gumroad_Embed_Settings_Page {

    /**
     * Initialize the settings page.
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_settings_page'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
    }

    /**
     * Add the settings page to the WooCommerce submenu.
     */
    public static function add_settings_page() {
        add_submenu_page(
            'woocommerce',
            __('Gumroad Embed Settings', 'gumroad-embed'),
            __('Gumroad Embed', 'gumroad-embed'),
            'manage_options',
            'gumroad-embed-settings',
            array(__CLASS__, 'settings_page_html')
        );
    }

    /**
     * Register settings for the Gumroad button customization.
     */
    public static function register_settings() {
        register_setting('gumroad_embed_settings', 'gumroad_button_width');
        register_setting('gumroad_embed_settings', 'gumroad_button_height');
        register_setting('gumroad_embed_settings', 'gumroad_button_font_size');
        register_setting('gumroad_embed_settings', 'gumroad_button_background_color');

        add_settings_section('gumroad_embed_section', '', null, 'gumroad-embed-settings');

        add_settings_field(
            'gumroad_button_width',
            __('Button Width (e.g., 100px or 100%)', 'gumroad-embed'),
            array(__CLASS__, 'settings_field_html'),
            'gumroad-embed-settings',
            'gumroad_embed_section',
            array(
                'label_for' => 'gumroad_button_width',
                'type' => 'text',
                'option_name' => 'gumroad_button_width',
                'description' => __('Set the width of the Gumroad button.', 'gumroad-embed'),
            )
        );

        add_settings_field(
            'gumroad_button_height',
            __('Button Height (e.g., 50px)', 'gumroad-embed'),
            array(__CLASS__, 'settings_field_html'),
            'gumroad-embed-settings',
            'gumroad_embed_section',
            array(
                'label_for' => 'gumroad_button_height',
                'type' => 'text',
                'option_name' => 'gumroad_button_height',
                'description' => __('Set the height of the Gumroad button.', 'gumroad-embed'),
            )
        );

        add_settings_field(
            'gumroad_button_font_size',
            __('Button Font Size (e.g., 16px)', 'gumroad-embed'),
            array(__CLASS__, 'settings_field_html'),
            'gumroad-embed-settings',
            'gumroad_embed_section',
            array(
                'label_for' => 'gumroad_button_font_size',
                'type' => 'text',
                'option_name' => 'gumroad_button_font_size',
                'description' => __('Set the font size of the Gumroad button.', 'gumroad-embed'),
            )
        );

        add_settings_field(
            'gumroad_button_background_color',
            __('Button Background Color (e.g., #000000)', 'gumroad-embed'),
            array(__CLASS__, 'settings_field_html'),
            'gumroad-embed-settings',
            'gumroad_embed_section',
            array(
                'label_for' => 'gumroad_button_background_color',
                'type' => 'text',
                'option_name' => 'gumroad_button_background_color',
                'description' => __('Set the background color of the Gumroad button.', 'gumroad-embed'),
            )
        );
    }

    /**
     * Output HTML for settings fields.
     */
    public static function settings_field_html($args) {
        $value = get_option($args['option_name'], '');
        printf(
            '<input type="%s" id="%s" name="%s" value="%s" class="regular-text" />',
            esc_attr($args['type']),
            esc_attr($args['label_for']),
            esc_attr($args['option_name']),
            esc_attr($value)
        );
        if (!empty($args['description'])) {
            printf('<p class="description">%s</p>', esc_html($args['description']));
        }
    }

    /**
     * Render the settings page.
     */
    public static function settings_page_html() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Gumroad Embed Settings', 'gumroad-embed'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('gumroad_embed_settings');
                do_settings_sections('gumroad-embed-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
