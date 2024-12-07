<div id="woo-ai-chatbot" class="woo-ai-chatbot <?php echo esc_attr(get_option('woo_ai_chatbot_position', 'bottom-right')); ?>">
    <div class="chat-header">
        <h3><?php echo esc_html(get_bloginfo('name')); ?> Assistant</h3>
        <button class="minimize-btn">−</button>
    </div>
    <div class="chat-messages">
        <div class="message bot">
            <?php echo esc_html(get_option('woo_ai_chatbot_welcome_message', 'Hi! How can I help you today?')); ?>
        </div>
    </div>
    <div class="chat-input">
        <input type="text" placeholder="Type your message..." aria-label="Chat message">
        <button type="submit">Send</button>
    </div>
</div>