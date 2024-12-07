<?php

namespace WooAIChatbot\Services;

class NLPService
{
    private $training_data;
    private $product_service;

    public function __construct(ProductService $product_service)
    {
        $this->product_service = $product_service;
        $this->training_data = $this->load_training_data();
    }

    private function load_training_data()
    {
        $file_path = plugin_dir_path(__FILE__) . 'intents.json'; // Absolute path to intents.json

        // Check if the file exists before attempting to read it
        if (file_exists($file_path)) {
            $json_content = file_get_contents($file_path);
            return json_decode($json_content, true); // Decode JSON into array
        } else {
            // Handle the error gracefully if the file doesn't exist
            return [
                'error' => 'Training data file not found.',
            ];
        }
    }

    public function process_message($message)
    {
        $intent = $this->detect_intent($message);

        switch ($intent) {
            case 'product.search':
                return $this->product_service->get_recommendations($message);
            case 'faq.returns':
                return [
                    'type' => 'faq',
                    'content' => get_option('woo_ai_chatbot_returns_message', 'You can return items within 30 days of purchase.')
                ];
            default:
                return [
                    'type' => 'general',
                    'content' => 'I\'m not sure I understand. Would you like to browse our products or check our FAQs?'
                ];
        }
    }

    private function detect_intent($message)
    {
        $message = strtolower($message);

        foreach ($this->training_data['intents'] as $intent => $data) {
            foreach ($data['patterns'] as $pattern) {
                if (strpos($message, strtolower($pattern)) !== false) {
                    return $intent;
                }
            }
        }

        return 'unknown';
    }
}
