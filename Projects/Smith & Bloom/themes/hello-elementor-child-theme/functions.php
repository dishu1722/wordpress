<?php
/*
 * This is the child theme for Hello Elementor theme, generated with Generate Child Theme plugin by catchthemes.
 *
 * (Please see https://developer.wordpress.org/themes/advanced-topics/child-themes/#how-to-create-a-child-theme)
 */
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_theme_enqueue_styles' );
function hello_elementor_child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
}
/*
 * Your code goes below
 */
// Enable menu support and register navigation menus
function custom_theme_setup() {
    // Enable menu support
    add_theme_support('menus');

    // Register menu locations
    register_nav_menus(array(
        'primary'   => __('Primary Menu', 'your-theme-textdomain'),
        'footer'    => __('Footer Menu', 'your-theme-textdomain')
    ));
}
add_action('after_setup_theme', 'custom_theme_setup');

// Move Tabs below the add to cart buttons
function move_tabs(){
  remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
  add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 70 );
}
add_action('wp', 'move_tabs');

// Hide variable product price range on single product page
add_filter( 'woocommerce_variable_price_html', 'remove_price_range_for_variable_products', 10, 2 );
function remove_price_range_for_variable_products( $price, $product ) {
    if ( is_product() ) {
        $price = '';
    }
    return $price;
}
