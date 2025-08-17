<?php

// Adding the Category Title and Category image in the Category listing page
function custom_category_header() {
    if ( is_product_category() ) {
        $term = get_queried_object();
        if ( $term && isset( $term->term_id ) ) {
            $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
            $image_url = wp_get_attachment_url( $thumbnail_id );

            echo '<div class="category-header">';
            echo '<h1 class="category-title">' . esc_html( $term->name ) . '</h1>';

            if ( !empty($image_url) ) {
                echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $term->name ) . '" class="category-image">';
            }
            echo '</div>';
        }
    }
}

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

// OR
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

// Cart icon with count
function custom_wc_cart_icon() {
    ?>
    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="custom-cart-link">
        <i class="fas fa-shopping-cart"></i>
        <span class="custom-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    </a>
    <?php
}

// Add cart markup to footer (so WooCommerce always has it loaded)
add_action('wp_footer', function() {
    echo '<div id="custom-cart-fragment" style="display:none;">';
    custom_wc_cart_icon();
    echo '</div>';
});

// Allow AJAX cart refresh
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    ob_start();
    custom_wc_cart_icon();
    $fragments['.custom-cart-link'] = ob_get_clean();
    return $fragments;
});

?>
