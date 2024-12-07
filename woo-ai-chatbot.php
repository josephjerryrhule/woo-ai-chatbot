<?php
/**
 * Plugin Name: WooCommerce AI Chatbot
 * Description: AI-powered chatbot for WooCommerce that learns and recommends products
 * Version: 1.0.0
 * Author: Your Name
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('WOO_AI_CHATBOT_VERSION', '1.0.0');
define('WOO_AI_CHATBOT_PATH', plugin_dir_path(__FILE__));
define('WOO_AI_CHATBOT_URL', plugin_dir_url(__FILE__));

// Autoloader for PHP classes
spl_autoload_register(function ($class) {
    $prefix = 'WooAIChatbot\\';
    $base_dir = WOO_AI_CHATBOT_PATH . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function woo_ai_chatbot_init() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>WooCommerce AI Chatbot requires WooCommerce to be installed and active.</p></div>';
        });
        return;
    }

    // Initialize main plugin class
    $plugin = new \WooAIChatbot\Plugin();
    $plugin->init();
}
add_action('plugins_loaded', 'woo_ai_chatbot_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    require_once WOO_AI_CHATBOT_PATH . 'includes/Activator.php';
    \WooAIChatbot\Activator::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    require_once WOO_AI_CHATBOT_PATH . 'includes/Deactivator.php';
    \WooAIChatbot\Deactivator::deactivate();
});