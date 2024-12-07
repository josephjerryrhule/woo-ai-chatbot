<?php
namespace WooAIChatbot\Services;

class ProductService {
    public function get_recommendations($query) {
        global $wpdb;

        // Get user interaction history
        $history = $this->get_user_history();
        
        // Query WooCommerce products
        $args = [
            'post_type' => 'product',
            'posts_per_page' => 5,
            'orderby' => 'popularity'
        ];

        if (!empty($history['preferred_categories'])) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $history['preferred_categories']
                ]
            ];
        }

        $products = wc_get_products($args);
        $results = [];

        foreach ($products as $product) {
            $results[] = [
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'image' => wp_get_attachment_url($product->get_image_id()),
                'link' => get_permalink($product->get_id())
            ];
        }

        // Store interaction
        $this->store_interaction($query, array_column($results, 'id'));

        return [
            'type' => 'products',
            'content' => $results
        ];
    }

    private function get_user_history() {
        global $wpdb;
        
        // Get current user's session or ID
        $user_id = get_current_user_id();
        $session_id = WC()->session ? WC()->session->get_customer_id() : null;

        // Query interaction history
        $table_name = $wpdb->prefix . 'woo_ai_chatbot_interactions';
        $history = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d OR session_id = %s ORDER BY timestamp DESC LIMIT 10",
            $user_id,
            $session_id
        ));

        return [
            'preferred_categories' => $this->analyze_category_preferences($history),
            'recent_searches' => array_column($history, 'query')
        ];
    }

    private function analyze_category_preferences($history) {
        $categories = [];
        
        foreach ($history as $interaction) {
            if (!empty($interaction->product_ids)) {
                $product_ids = explode(',', $interaction->product_ids);
                foreach ($product_ids as $product_id) {
                    $product_categories = wp_get_post_terms($product_id, 'product_cat');
                    foreach ($product_categories as $category) {
                        $categories[$category->term_id] = ($categories[$category->term_id] ?? 0) + 1;
                    }
                }
            }
        }

        arsort($categories);
        return array_keys(array_slice($categories, 0, 3));
    }

    public function store_interaction($query, $product_ids) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'woo_ai_chatbot_interactions';
        $user_id = get_current_user_id();
        $session_id = WC()->session ? WC()->session->get_customer_id() : null;

        $wpdb->insert(
            $table_name,
            [
                'user_id' => $user_id,
                'session_id' => $session_id,
                'query' => $query,
                'product_ids' => implode(',', $product_ids),
                'timestamp' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s', '%s']
        );

        return $wpdb->insert_id;
    }

    public function store_feedback($interaction_id, $feedback) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'woo_ai_chatbot_interactions';
        
        $wpdb->update(
            $table_name,
            ['feedback' => $feedback],
            ['id' => $interaction_id],
            ['%s'],
            ['%d']
        );
    }
}