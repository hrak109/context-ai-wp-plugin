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
        'Context AI',
        'Context AI',
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
        <h1>Context AI Settings</h1>
        <p>Sign in with your Google Account to connect your business.</p>

        <form method="post" action="options.php" id="context-ai-form">
            <?php settings_fields('context_ai_options'); ?>
            <?php do_settings_sections('context_ai_options'); ?>
            
            <div id="g_id_onload"
                 data-client_id="YOUR_GOOGLE_CLIENT_ID"
                 data-callback="handleCredentialResponse"
                 data-auto_prompt="false">
            </div>
            <div class="g_id_signin"
                 data-type="standard"
                 data-size="large"
                 data-theme="outline"
                 data-text="sign_in_with"
                 data-shape="rectangular"
                 data-logo_alignment="left">
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
            
            <?php submit_button('Update Connection Manually', 'secondary'); ?>
        </form>
    </div>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        function handleCredentialResponse(response) {
            // Decode the JWT credential
            const responsePayload = decodeJwtResponse(response.credential);

            console.log("ID: " + responsePayload.sub);
            console.log('Full Name: ' + responsePayload.name);
            console.log('Given Name: ' + responsePayload.given_name);
            console.log('Family Name: ' + responsePayload.family_name);
            console.log("Image URL: " + responsePayload.picture);
            console.log("Email: " + responsePayload.email);

            // Update UI
            document.getElementById('profile-card').style.display = 'block';
            document.getElementById('profile-img').src = responsePayload.picture;
            document.getElementById('profile-name').innerText = responsePayload.name;
            document.getElementById('profile-email').innerText = responsePayload.email;

            // Update Inputs
            document.getElementById('input_client_id').value = responsePayload.email; // Using Email as ID for now
            document.getElementById('input_client_name').value = responsePayload.name;
            document.getElementById('input_client_email').value = responsePayload.email;
            document.getElementById('input_client_picture').value = responsePayload.picture;

            // Auto-submit form to save options
            document.getElementById('context-ai-form').submit();
        }

        function decodeJwtResponse(token) {
            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
            return JSON.parse(jsonPayload);
        }
    </script>
    <?php
}
?>
