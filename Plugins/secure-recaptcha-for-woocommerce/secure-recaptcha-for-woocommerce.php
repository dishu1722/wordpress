<?php
/*
Plugin Name: Secure reCAPTCHA for WooCommerce
Description: Protects your WooCommerce checkout from spam and fraudulent orders using Google reCAPTCHA v2.
Version: 1.0
Author: Diksha Sharma
License: GPL2
*/

// === Add settings page in admin ===
add_action('admin_menu', function() {
    add_options_page(
        'Secure reCAPTCHA Settings',
        'Secure reCAPTCHA',
        'manage_options',
        'secure-recaptcha',
        'secure_recaptcha_settings_page'
    );
});

add_action('admin_init', function() {
    register_setting('secure_recaptcha_options', 'secure_recaptcha_site_key');
    register_setting('secure_recaptcha_options', 'secure_recaptcha_secret_key');
});

function secure_recaptcha_settings_page() {
    ?>
    <div class="wrap">
        <h1>Secure reCAPTCHA Settings</h1>
        <p>Enter your Google reCAPTCHA v2 Site and Secret keys below. You can create keys at <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a>.</p>
        <form method="post" action="options.php">
            <?php settings_fields('secure_recaptcha_options'); ?>
            <?php do_settings_sections('secure_recaptcha_options'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Site Key</th>
                    <td><input type="text" name="secure_recaptcha_site_key" value="<?php echo esc_attr(get_option('secure_recaptcha_site_key')); ?>" style="width:400px;"></td>
                </tr>
                <tr>
                    <th scope="row">Secret Key</th>
                    <td><input type="text" name="secure_recaptcha_secret_key" value="<?php echo esc_attr(get_option('secure_recaptcha_secret_key')); ?>" style="width:400px;"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// === Add reCAPTCHA to WooCommerce checkout ===
add_action('woocommerce_after_checkout_form', function() {
    $site_key = get_option('secure_recaptcha_site_key');
    if ($site_key) {
        echo '<div class="g-recaptcha" data-sitekey="' . esc_attr($site_key) . '"></div>';
        echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
    }
});

// === Verify reCAPTCHA before processing order ===
add_action('woocommerce_checkout_process', function() {
    $secret_key = get_option('secure_recaptcha_secret_key');

    if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
        wc_add_notice(__('Please complete the reCAPTCHA verification.', 'woocommerce'), 'error');
        return;
    }

    $response = wp_remote_post("https://www.google.com/recaptcha/api/siteverify", [
        'body' => [
            'secret' => $secret_key,
            'response' => sanitize_text_field($_POST['g-recaptcha-response']),
        ],
    ]);

    $result = json_decode(wp_remote_retrieve_body($response));

    if (empty($result->success)) {
        wc_add_notice(__('reCAPTCHA verification failed. Please try again.', 'woocommerce'), 'error');
    }
});
