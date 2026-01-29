<?php
/**
 * Plugin Name: Context AI for Businesses
 * Description: Business chat plugin for WordPress. Upload your business information and let Context AI handle your customer questions and inquiries!
 * Version: 2.0
 * Author: Hee Bae
 */

function ai_chat_enqueue_scripts() {
    wp_enqueue_style('ai-chat-style', plugin_dir_url(__FILE__) . 'chat.css');
    wp_enqueue_script('ai-chat-script', plugin_dir_url(__FILE__) . 'chat.js', array('jquery'), null, true);

    // Pass the API URL and Client ID to JavaScript
    wp_localize_script('ai-chat-script', 'aiChat', array(
        'apiUrl' => 'https://api.oakhillpines.com/api/oakhillpines',
        'clientId' => get_option('context_ai_client_id') // Use stored API Key as client ID
    ));
}
add_action('wp_enqueue_scripts', 'ai_chat_enqueue_scripts');

// Include Admin Settings
require_once plugin_dir_path(__FILE__) . 'admin.php';

function ai_chat_shortcode() {
    ob_start(); ?>
    <div id="ai-chat-wrapper">
        <button id="ai-chat-toggle" class="chat-toggle-btn">
             <!-- SVG Icon for Chat -->
             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        </button>
        <div id="ai-chat-container" class="hidden">
            <div id="ai-chat-header">
                <span>Context AI</span>
                <button id="ai-chat-close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div id="ai-chat-messages">
                 <!-- Welcome Message -->
                 <div class="chat-message ai">Hello! How can I help you today?</div>
            </div>
            <div id="ai-chat-input-area">
                <input type="text" id="ai-chat-input" placeholder="Type your message..." />
                <button id="ai-chat-send">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('ai_chat', 'ai_chat_shortcode');
