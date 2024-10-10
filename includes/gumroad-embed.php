<?php
/**
 * Main class for the Gumroad Embed plugin.
 */
class Gumroad_Embed
{

    private static $instance = null;

    /**
     * Singleton pattern to get the instance of this class.
     *
     * @return Gumroad_Embed
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor to initialize hooks.
     */
    private function __construct()
    {
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_gumroad_checkbox'));
        add_action('woocommerce_process_product_meta', array($this, 'save_gumroad_checkbox'));
        add_action('woocommerce_after_add_to_cart_button', array($this, 'replace_with_gumroad_button'));
        // Enqueue scripts and styles.
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Adds the Gumroad embed checkbox to external/affiliate products.
     */
    public function add_gumroad_checkbox()
    {
        global $product_object;

        // Check if the product type is external/affiliate.
        if ($product_object && 'external' === $product_object->get_type()) {
            woocommerce_wp_checkbox(
                array(
                    'id' => '_use_gumroad_embed',
                    'label' => __('Use Gumroad Embed', 'gumroad-embed'),
                    'description' => __('Replace the buy button with Gumroad embed.', 'gumroad-embed'),
                )
            );

            // Add a nonce for security
            wp_nonce_field('save_gumroad_checkbox_nonce_action', 'save_gumroad_checkbox_nonce');
        }
    }

    /**
     * Saves the value of the Gumroad embed checkbox.
     *
     * @param int $post_id Product ID.
     */
    public function save_gumroad_checkbox($post_id)
    {
        // Check if the nonce is set.
        if (isset($_POST['save_gumroad_checkbox_nonce'])) {
            // Unslash and sanitize the nonce before verifying it.
            $nonce = sanitize_text_field(wp_unslash($_POST['save_gumroad_checkbox_nonce']));

            // Verify the nonce.
            if (!wp_verify_nonce($nonce, 'save_gumroad_checkbox_nonce_action')) {
                return; // Nonce verification failed.
            }
        } else {
            return; // No nonce provided, fail.
        }

        // Check if this is an autosave, and if so, don't save the data.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $use_gumroad_embed = isset($_POST['_use_gumroad_embed']) ? 'yes' : 'no';
        update_post_meta($post_id, '_use_gumroad_embed', $use_gumroad_embed);
    }

    /**
     * Replaces the external buy button with the Gumroad embed code.
     */
    public function replace_with_gumroad_button()
    {
        global $product;

        if ($product && 'external' === $product->get_type()) {
            $use_gumroad_embed = get_post_meta($product->get_id(), '_use_gumroad_embed', true);

            if ('yes' === $use_gumroad_embed) {
                $product_url = esc_url($product->get_product_url());

                if (empty($product_url)) {
                    return; // Fail-safe: Do nothing if no URL is set.
                }

                // Get the custom button styles from settings.
                $button_width = get_option('gumroad_button_width', 'auto');
                $button_height = get_option('gumroad_button_height', 'auto');
                $font_size = get_option('gumroad_button_font_size', '16px');
                $background_color = get_option('gumroad_button_background_color', '#000000');

                // Output the Gumroad embed code with customizable styles.
                echo sprintf(
                    '<a class="gumroad-button" href="%s" style="width:%s; height:%s; font-size:%s; background-color:%s;">Buy on</a>',
                    esc_url($product_url),
                    esc_attr($button_width),
                    esc_attr($button_height),
                    esc_attr($font_size),
                    esc_attr($background_color)
                );

                // Optionally hide the default external product button.
                echo '<style> .single_add_to_cart_button { display: none !important; } </style>';
            }
        }
    }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('gumroad', 'https://gumroad.com/js/gumroad.js', array(), '1.0', true);
    }
}
