// Redirect the Cart page 'proceed to checkout' button to a custom URL
add_action( 'template_redirect', function() {
    if ( is_checkout() && ! is_order_received_page() ) {
        wp_redirect( 'https://buffethire.com.au/request-a-qoute/' );
        exit;
    }
});

// Shortcode to display WooCommerce cart items
function custom_cart_items_list() {
    if ( WC()->cart->is_empty() ) return 'No items in cart.';
    
    $items = WC()->cart->get_cart();
    $output = '';
    foreach ( $items as $item => $values ) {
        $product = $values['data']->get_name();
        //$quantity = $values['quantity'];
        //$price = wc_price( $values['data']->get_price() );
        //$output .= $product . ' - Qty: ' . $quantity . ' - Price: ' . $price . "\n";
        $output .= $product . "\n";
    }
    return $output;
}
add_shortcode( 'cart_items_list', 'custom_cart_items_list' );

//OR
function custom_cart_items_list() {
    // Only run on frontend and if WooCommerce cart is available
    if ( is_admin() ) {
        // Don't run shortcode in admin area (like page editor)
        return '';
    }

    if ( ! class_exists( 'WooCommerce' ) ) {
        return 'WooCommerce not active.';
    }

    if ( ! WC()->cart ) {
        return 'Cart not initialized.';
    }

    if ( WC()->cart->is_empty() ) {
        return 'No items in cart.';
    }
    
    $items = WC()->cart->get_cart();
    $output = '';
    foreach ( $items as $item => $values ) {
        $product = $values['data']->get_name();
        $output .= $product . "\n";
    }
    return nl2br( $output ); // Convert new lines to <br> for HTML output
}
add_shortcode( 'cart_items_list', 'custom_cart_items_list' );
