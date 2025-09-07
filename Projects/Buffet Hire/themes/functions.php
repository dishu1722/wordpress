

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


// Show some text with the price in shop page
function custom_woocommerce_price_html( $price, $product ) {
    return $price . ' per day';
}
add_filter( 'woocommerce_get_price_html', 'custom_woocommerce_price_html', 10, 2 );


// Moving the product title to the top of the description side
function move_product_title_above_page() {
    if ( is_product() ) {
        global $post;
        echo '<div class="custom-title-wrapper">';
        echo '<h1 class="custom-product-title">' . get_the_title($post->ID) . '</h1>';
		echo '</div>';
    }
}
// Remove default title from single product page
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

// Add title to the top of the page
add_action( 'woocommerce_before_main_content', 'move_product_title_above_page', 5 );


// Redirect the Cart page 'proceed to checkout' button to a custom URL
add_action( 'template_redirect', function() {
    if ( is_checkout() && ! is_order_received_page() ) {
        wp_redirect( 'https://buffethire.com.au/request-a-qoute/' );
        exit;
    }
});


// Shortcode to display WooCommerce cart items with quantity and url
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
    foreach ( $items as $item ) {
        $product = $item['data'];
		$product_id  = $item['product_id'];
		$product_url = get_permalink( $product_id );
		$product_name = $product->get_name();
        $quantity = $item['quantity'];
        $output .= $product_name . ' - Qty: ' . intval( $quantity ) . ' - ' . $product_url . "\n";
    }
    return $output; 
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


// to add class in the body(single product) for a particular category
add_filter( 'body_class', 'add_package_single_class_by_category' );
function add_package_single_class_by_category( $classes ) {
    if ( is_product() ) {
        $terms = get_the_terms( get_the_ID(), 'product_cat' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                if ( strtolower( $term->name ) === 'tableware package' ) {
                    $classes[] = 'package-single';
                    break;
                }
            }
        }
    }
    return $classes;
}


// To display Related products of particular category by SHORTCODE
function tableware_package_related_shortcode() {
    if ( ! is_product() ) return '';
    global $product;
	
    $product_id = $product->get_id();
    $terms = get_the_terms( $product_id, 'product_cat' );

    if ( $terms && ! is_wp_error( $terms ) ) {
        $has_tableware_package = false;

        foreach ( $terms as $term ) {
            if ( $term->slug === 'tableware-package' ) {
                $has_tableware_package = true;
                break;
            }
        }

        if ( $has_tableware_package ) {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 4,
                'post__not_in' => array( $product_id ),
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => 'tableware-package',
                    ),
                ),
            );

            $related_query = new WP_Query( $args );

            if ( $related_query->have_posts() ) {
                ob_start();
                echo '<section class="related-products-tableware">';
                echo '<div class="container">';
                echo '<h2>Related Products</h2>';
                echo '<ul class="products">';
                while ( $related_query->have_posts() ) {
                    $related_query->the_post();
                    wc_get_template_part( 'content', 'product' );
                }
                echo '</ul></div></section>';
                wp_reset_postdata();
                return ob_get_clean();
            }
        }
    }

    return '';
}
add_shortcode( 'tableware_related', 'tableware_package_related_shortcode' );

// Bookings For WooCommerce Plugin customization
// To add Base cosr in the Product listing page
add_action( 'woocommerce_after_shop_loop_item_title', 'inject_mwb_base_cost_price', 15 );
function inject_mwb_base_cost_price() {
    global $product;

    if ( $product->get_type() === 'mwb_booking' ) {
        $base_cost = get_post_meta( $product->get_id(), 'mwb_mbfw_booking_base_cost', true );

        if ( $base_cost && $base_cost > 0 ) {
            echo '<span class="price">' . wc_price( $base_cost ) . '</span>';
        }
    }
}

// Replace excerpt with price for product search results
add_filter( 'the_excerpt', function( $excerpt ) {
    if ( is_search() && get_post_type() === 'product' ) {
        global $post;
        $product = wc_get_product( $post->ID );

        if ( $product ) {
            if ( $product->get_type() === 'mwb_booking' ) {
                $base_cost = get_post_meta( $product->get_id(), 'mwb_mbfw_booking_base_cost', true );
                if ( $base_cost && $base_cost > 0 ) {
                    return '<span class="price">' . wc_price( $base_cost ) . '</span>';
                }
            }

            // Fallback for normal products
            return '<span class="price">' . $product->get_price_html() . '</span>';
        }
    }

    return $excerpt;
}, 20 );

?>
