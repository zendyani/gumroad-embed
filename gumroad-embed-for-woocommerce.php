<?php
/**
 * Plugin Name: Gumroad Embed for WooCommerce
 * Plugin URI: https://example.com/
 * Description: Adds a Gumroad embed button to WooCommerce external products.
 * Version: 0.9
 * Author: Belakhdar Abdeldjalil
 * Author URI: https://example.com/
 * License: GPLv3
 * Text Domain: gumroad-embed-for-woocommerce
 * 
 * @package GumroadEmbedForWooCommerce
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class.
 */
class Gumroad_Embed_For_WooCommerce {

    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }

    /**
     * Initialize the plugin.
     */
    public function init() {
        // Check if WooCommerce is active.
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }

        // Add Gumroad Embed Checkbox to External/Affiliate Products.
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_gumroad_checkbox'));

        // Save Gumroad Embed Checkbox Value.
        add_action('woocommerce_process_product_meta', array($this, 'save_gumroad_checkbox'));

        // Replace External Buy Button with Gumroad Embed Code.
        add_action('woocommerce_after_add_to_cart_button', array($this, 'replace_with_gumroad_button'));

        // Enqueue scripts and styles.
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Display admin notice if WooCommerce is not active.
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="error">
            <p><?php esc_html_e('Gumroad Embed for WooCommerce requires WooCommerce to be installed and active.', 'gumroad-embed-for-woocommerce'); ?></p>
        </div>
        <?php
    }

    /**
     * Add Gumroad Embed Checkbox to External/Affiliate Products.
     */
    public function add_gumroad_checkbox() {
        global $product_object;

        // Check if the product type is external/affiliate.
        if ($product_object && 'external' === $product_object->get_type()) {
            woocommerce_wp_checkbox(
                array(
                    'id'          => '_use_gumroad_embed',
                    'label'       => __('Use Gumroad Embed', 'gumroad-embed-for-woocommerce'),
                    'description' => __('Replace the buy button with Gumroad embed.', 'gumroad-embed-for-woocommerce'),
                )
            );
        }
    }

    /**
     * Save Gumroad Embed Checkbox Value.
     *
     * @param int $post_id The post ID.
     */
    public function save_gumroad_checkbox($post_id) {
        $use_gumroad_embed = isset($_POST['_use_gumroad_embed']) ? 'yes' : 'no';
        update_post_meta($post_id, '_use_gumroad_embed', sanitize_text_field($use_gumroad_embed));
    }

    /**
     * Replace External Buy Button with Gumroad Embed Code.
     */
    public function replace_with_gumroad_button() {
        global $product;

        if ($product && 'external' === $product->get_type()) {
            $use_gumroad_embed = get_post_meta($product->get_id(), '_use_gumroad_embed', true);

            if ('yes' === $use_gumroad_embed) {
                // Get the external product URL (this will be used as the Gumroad link).
                $product_url = esc_url($product->get_product_url());

                // Output the Gumroad embed code with the correct URL.
                ?>
                <a class="gumroad-button" href="<?php echo $product_url; ?>"><?php esc_html_e('Buy on Gumroad', 'gumroad-embed-for-woocommerce'); ?></a>
                <style> .single_add_to_cart_button { display: none !important; } </style>
                <?php
            }
        }
    }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_scripts() {
        wp_enqueue_script('gumroad', 'https://gumroad.com/js/gumroad.js', array(), '1.0', true);
    }
}

// Initialize the plugin.
new Gumroad_Embed_For_WooCommerce();