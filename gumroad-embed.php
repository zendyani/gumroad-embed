<?php
/**
 * Plugin Name: Gumroad Embed
 * Plugin URI:  https://www.captain-design.com
 * Description: Adds a Gumroad embed option for external/affiliate products for WooCommerce with customizable button styles.
 * Version:     1.0.0
 * Author:      Abdeldjalil Belakhdar
 * Author URI:  https://www.captain-design.com
 * License:     GPLv2 or later
 * Text Domain: gumroad-embed
 * Domain Path: /languages
 */

defined('ABSPATH') || exit; // Prevent direct access.

if (!class_exists('Gumroad_Embed')) {

    /**
     * Check if WooCommerce is active
     */
    function gumroad_embed_check_woocommerce() {
        // Check if WooCommerce is active.
        if (!class_exists('WooCommerce')) {
            // Display an admin notice and deactivate the plugin if WooCommerce is not installed.
            add_action('admin_notices', 'gumroad_embed_woocommerce_missing_notice');
            // deactivate_plugins(plugin_basename(__FILE__));
        }
    }

    /**
     * Display admin notice if WooCommerce is missing.
     */
    function gumroad_embed_woocommerce_missing_notice() {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>' . esc_html__('WooCommerce Gumroad Embed requires WooCommerce to be installed and active.', 'gumroad-embed') . '</strong></p>';
        echo '</div>';
    }

    // Run WooCommerce check on plugin activation and on the plugins_loaded action.
    register_activation_hook(__FILE__, 'gumroad_embed_check_woocommerce');
    add_action('plugins_loaded', 'gumroad_embed_check_woocommerce');

    // Include the necessary classes.
    include_once plugin_dir_path(__FILE__) . 'includes/gumroad-embed.php';
    include_once plugin_dir_path(__FILE__) . 'admin/gumroad-embed-settings-page.php';

    // Initialize the plugin and the settings page.
    function gumroad_embed_init() {
        Gumroad_Embed::get_instance();
        Gumroad_Embed_Settings_Page::init(); // Initialize settings page.
    }
    add_action('plugins_loaded', 'gumroad_embed_init');
}
