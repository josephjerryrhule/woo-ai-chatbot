<?php
namespace WooAIChatbot;

class Activator {
    public static function activate() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'woo_ai_chatbot_interactions';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            session_id varchar(100) DEFAULT NULL,
            query text NOT NULL,
            product_ids text NOT NULL,
            feedback varchar(20) DEFAULT 'neutral',
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY session_id (session_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Add default options
        add_option('woo_ai_chatbot_enabled', true);
        add_option('woo_ai_chatbot_position', 'bottom-right');
        add_option('woo_ai_chatbot_welcome_message', 'Hi! How can I help you today?');
    }
}