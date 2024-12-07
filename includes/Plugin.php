<?php

namespace WooAIChatbot;

use WooAIChatbot\Services\NLPService;
use WooAIChatbot\Services\ProductService;


class Plugin
{
    private $nlp_service;
    private $product_service;

    public function init()
    {
        require_once WOO_AI_CHATBOT_PATH . 'includes/Services/NLPService.php';
        require_once WOO_AI_CHATBOT_PATH . 'includes/Services/ProductService.php';

        // Initialize the ProductService
        $this->product_service = new ProductService();

        // Pass the ProductService instance to the NLPService constructor
        $this->nlp_service = new NLPService($this->product_service);

        // Register REST API endpoints
        add_action('rest_api_init', [$this, 'register_rest_routes']);

        // Enqueue frontend scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        // Add chat widget to footer
        add_action('wp_footer', [$this, 'render_chat_widget']);

        // Add admin menu
        add_action('admin_menu', [$this, 'add_admin_menu']);

        // Register settings
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_rest_routes()
    {
        error_log('Registering REST API routes'); // Debug line to check if this function runs

        register_rest_route('woo-ai-chatbot/v1', '/chat', [
            'methods' => 'GET',
            'callback' => [$this, 'handle_chat_message'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handle_chat_message($request)
    {
        error_log('Handling chat message'); // Check if the function is being triggered

        $message = $request->get_param('message');

        // Ensure a message was sent
        if (empty($message)) {
            return new \WP_REST_Response(['error' => 'Message is required'], 400);
        }

        // Process the message using NLPService or any service you use
        $response = $this->nlp_service->process_message($message);

        // Return the processed response as JSON
        return rest_ensure_response($response);
    }

    public function enqueue_scripts()
    {
        // Enqueue your plugin's styles
        wp_enqueue_style(
            'woo-ai-chatbot-style',
            WOO_AI_CHATBOT_URL . 'assets/css/chatbot.css',
            [],
            '1.0.0'
        );

        // Enqueue your plugin's scripts
        wp_enqueue_script(
            'woo-ai-chatbot-script',
            WOO_AI_CHATBOT_URL . 'assets/js/chatbot.js',
            ['jquery'],
            '1.0.0',
            true
        );

        // Pass data to your JavaScript (optional)
        wp_localize_script(
            'woo-ai-chatbot-script',
            'wooAIChatbot',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'rest_url' => esc_url(rest_url('woo-ai-chatbot/v1/')),
                'nonce' => wp_create_nonce('wp_rest'),
            ]
        );
    }

    public function register_settings()
    {
        register_setting('woo_ai_chatbot_options', 'woo_ai_chatbot_enabled');
        register_setting('woo_ai_chatbot_options', 'woo_ai_chatbot_position');
        register_setting('woo_ai_chatbot_options', 'woo_ai_chatbot_welcome_message');
        register_setting('woo_ai_chatbot_options', 'woo_ai_chatbot_consumer_key');
        register_setting('woo_ai_chatbot_options', 'woo_ai_chatbot_consumer_secret');
    }

    public function add_admin_menu()
    {
        add_menu_page(
            'WooAI Chatbot Settings', // Page title
            'WooAI Chatbot',          // Menu title
            'manage_options',         // Capability
            'woo_ai_chatbot',         // Menu slug
            [$this, 'render_admin_page'], // Callback function to display content
            'dashicons-format-chat',  // Icon
            20                        // Position
        );
    }

    public function render_admin_page()
    {
        $template_path = WOO_AI_CHATBOT_PATH . 'templates/admin-page.php';

        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo '<div class="notice notice-error"><p>Admin settings template not found.</p></div>';
        }
    }

    public function render_chat_widget()
    {
        $template_path = WOO_AI_CHATBOT_PATH . 'templates/chat-widget.php';

        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo '<div>Error: Chat widget template not found.</div>';
        }
    }
}
