<?php
/**
 * Plugin Name: Gumroad Embed for WooCommerce
 * Plugin URI: https://example.com/
 * Description: Automatically adds a Gumroad embed button to WooCommerce external products with Gumroad URLs.
 * Version: 0.9
 * Author: Abdeldjalil
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

    /**
     * Constructor.
     */
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

        // Replace External Buy Button with Gumroad Embed Code.
        add_filter('woocommerce_external_add_to_cart', array($this, 'maybe_replace_with_gumroad_button'), 10);

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
     * Replace External Buy Button with Gumroad Embed Code if the URL is a Gumroad URL.
     *
     * @param string $button_html The original button HTML.
     * @return string Modified button HTML.
     */
    public function maybe_replace_with_gumroad_button($button_html) {
        global $product;

        if ($product && 'external' === $product->get_type()) {
            $product_url = $product->get_product_url();

            if ($this->is_gumroad_url($product_url)) {
                ob_start();
                ?>
                <a class="gumroad-button" href="<?php echo esc_url($product_url); ?>"><?php esc_html_e('Buy on Gumroad', 'gumroad-embed-for-woocommerce'); ?></a>
                <?php
                $button_html = ob_get_clean();
            }
        }

        return $button_html;
    }

    /**
     * Check if the given URL is a Gumroad URL.
     *
     * @param string $url The URL to check.
     * @return boolean True if it's a Gumroad URL, false otherwise.
     */
    private function is_gumroad_url($url) {
        return (strpos($url, 'gumroad.com') !== false);
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

/**
 * Activation hook.
 */
function gumroad_embed_activate() {
    // Activation tasks if needed.
}
register_activation_hook(__FILE__, 'gumroad_embed_activate');

/**
 * Deactivation hook.
 */
function gumroad_embed_deactivate() {
    // Deactivation tasks if needed.
}
register_deactivation_hook(__FILE__, 'gumroad_embed_deactivate');