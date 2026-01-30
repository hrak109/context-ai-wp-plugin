<?php
function context_ai_register_settings() {
    register_setting('context_ai_options', 'context_ai_client_id');
    register_setting('context_ai_options', 'context_ai_client_name');
    register_setting('context_ai_options', 'context_ai_client_email');
    register_setting('context_ai_options', 'context_ai_client_picture');
}
add_action('admin_init', 'context_ai_register_settings');

function context_ai_add_admin_menu() {
    add_menu_page(
        'hai Context AI',
        'hai Context AI',
        'manage_options',
        'context_ai',
        'context_ai_options_page',
        'dashicons-format-chat',
        100
    );
}
add_action('admin_menu', 'context_ai_add_admin_menu');

function context_ai_options_page() {
    ?>
    <div class="wrap">
        <h1>hai Context AI Settings</h1>
        <p>Connect your business to enable AI chat on your website.</p>

        <form method="post" action="options.php" id="context-ai-form">
            <?php settings_fields('context_ai_options'); ?>
            <?php do_settings_sections('context_ai_options'); ?>
            
            <div id="auth-section" style="margin-bottom: 20px;">
                <button type="button" id="connect-google-btn" class="button button-primary" style="display: flex; align-items: center; gap: 8px; padding: 0 16px; height: 40px;">
                    <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/><path d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.96H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.041l3.007-2.334z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 3.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg>
                    Connect with Google
                </button>
            </div>

            <div id="profile-card" style="margin-top: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 8px; max-width: 400px; display: <?php echo get_option('context_ai_client_email') ? 'block' : 'none'; ?>;">
                <h3>Connected Account</h3>
                <img id="profile-img" src="<?php echo esc_attr(get_option('context_ai_client_picture')); ?>" style="width: 50px; height: 50px; border-radius: 50%; vertical-align: middle;">
                <span id="profile-name" style="font-weight: bold; margin-left: 10px;"><?php echo esc_html(get_option('context_ai_client_name')); ?></span>
                <p id="profile-email" style="color: gray;"><?php echo esc_html(get_option('context_ai_client_email')); ?></p>
            </div>

            <!-- Hidden Inputs to store data -->
            <input type="hidden" name="context_ai_client_id" id="input_client_id" value="<?php echo esc_attr(get_option('context_ai_client_id')); ?>">
            <input type="hidden" name="context_ai_client_name" id="input_client_name" value="<?php echo esc_attr(get_option('context_ai_client_name')); ?>">
            <input type="hidden" name="context_ai_client_email" id="input_client_email" value="<?php echo esc_attr(get_option('context_ai_client_email')); ?>">
            <input type="hidden" name="context_ai_client_picture" id="input_client_picture" value="<?php echo esc_attr(get_option('context_ai_client_picture')); ?>">
            
            <?php submit_button('Update Connection Manually', 'secondary', 'submit_manual'); ?>
        </form>
    </div>

    <script>
        document.getElementById('connect-google-btn').addEventListener('click', () => {
            const width = 500;
            const height = 600;
            const left = (window.screen.width / 2) - (width / 2);
            const top = (window.screen.height / 2) - (height / 2);
            
            const authWindow = window.open(
                'https://api.oakhillpines.com/api/oakhillpines/auth/google',
                'ContextAIAuth',
                `width=${width},height=${height},left=${left},top=${top}`
            );
        });

        window.addEventListener('message', (event) => {
            // Check origin for security
            if (event.origin !== 'https://api.oakhillpines.com') return;

            if (event.data.type === 'CONTEXT_AI_AUTH_SUCCESS') {
                const data = event.data.data;

                // Update UI
                document.getElementById('profile-card').style.display = 'block';
                document.getElementById('profile-img').src = data.picture_url;
                document.getElementById('profile-name').innerText = data.name;
                document.getElementById('profile-email').innerText = data.email;

                // Update Inputs
                document.getElementById('input_client_id').value = data.api_key;
                document.getElementById('input_client_name').value = data.name;
                document.getElementById('input_client_email').value = data.email;
                document.getElementById('input_client_picture').value = data.picture_url;

                // Auto-submit form to save options
                HTMLFormElement.prototype.submit.call(document.getElementById('context-ai-form'));
            }
        });
    </script>
    <?php
}
?>
