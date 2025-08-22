<?php

// Shortcode for the Single product descrition to add in the accordion
add_shortcode( 'product_description', 'show_product_description' );

function show_product_description() {
    if ( ! is_product() ) return '';

    global $product;
    if ( ! $product ) return '';

    ob_start();
    echo '<div class="woocommerce-product-details accordion-design">';
    echo '<div class="attribute-accordion">';
    echo '<h3 class="accordion-toggle">Product Description <span class="accordion-icon"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span></h3>';
    echo '<div class="accordion-content" style="display:none;">';
    echo wpautop( wptexturize( $product->get_description() ) );
    echo '</div></div></div>';
    return ob_get_clean();
}

