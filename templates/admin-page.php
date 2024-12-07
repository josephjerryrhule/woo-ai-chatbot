<div class="wrap">
    <h1>WooCommerce AI Chatbot Settings</h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('woo_ai_chatbot_options'); ?>
        <?php do_settings_sections('woo_ai_chatbot_options'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">Enable Chatbot</th>
                <td>
                    <input type="checkbox" name="woo_ai_chatbot_enabled" value="1" 
                        <?php checked(get_option('woo_ai_chatbot_enabled'), 1); ?>>
                </td>
            </tr>
            <tr>
                <th scope="row">Widget Position</th>
                <td>
                    <select name="woo_ai_chatbot_position">
                        <option value="bottom-right" <?php selected(get_option('woo_ai_chatbot_position'), 'bottom-right'); ?>>
                            Bottom Right
                        </option>
                        <option value="bottom-left" <?php selected(get_option('woo_ai_chatbot_position'), 'bottom-left'); ?>>
                            Bottom Left
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">Welcome Message</th>
                <td>
                    <input type="text" name="woo_ai_chatbot_welcome_message" 
                           value="<?php echo esc_attr(get_option('woo_ai_chatbot_welcome_message')); ?>" 
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">WooCommerce API Consumer Key</th>
                <td>
                    <input type="text" name="woo_ai_chatbot_consumer_key" 
                           value="<?php echo esc_attr(get_option('woo_ai_chatbot_consumer_key')); ?>" 
                           class="regular-text">
                    <p class="description">Generate this in WooCommerce → Settings → Advanced → REST API</p>
                </td>
            </tr>
            <tr>
                <th scope="row">WooCommerce API Consumer Secret</th>
                <td>
                    <input type="password" name="woo_ai_chatbot_consumer_secret" 
                           value="<?php echo esc_attr(get_option('woo_ai_chatbot_consumer_secret')); ?>" 
                           class="regular-text">
                    <p class="description">Generate this in WooCommerce → Settings → Advanced → REST API</p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>

    <div class="chatbot-stats">
        <h2>Chatbot Statistics</h2>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'woo_ai_chatbot_interactions';
        $total_interactions = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $positive_feedback = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE feedback = 'positive'");
        ?>
        <p>Total Interactions: <?php echo esc_html($total_interactions); ?></p>
        <p>Positive Feedback: <?php echo esc_html($positive_feedback); ?></p>
    </div>
</div>