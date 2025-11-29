<?php
/*
 * This is the child theme for Hello Elementor theme, generated with Generate Child Theme plugin by catchthemes.
 *
 * (Please see https://developer.wordpress.org/themes/advanced-topics/child-themes/#how-to-create-a-child-theme)
 */

// Load Css file
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_theme_enqueue_styles' );
function hello_elementor_child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
}

// Load Script file
add_action('wp_enqueue_scripts', 'my_custom_scripts');
function my_custom_scripts() {
wp_enqueue_script(
    'scripts-js',
    get_stylesheet_directory_uri() . '/js/scripts.js', // CORRECTED TO MATCH YOUR PATH
    array('jquery'),
    filemtime(get_stylesheet_directory() . '/js/scripts.js'), // CORRECTED HERE TOO
    true
);
}


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/* Load Linen Calculator CSS & JS */
function linen_calculator_assets() {
    
    // Check if WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    // CSS
    wp_enqueue_style(
        'linen-calculator-css',
        get_stylesheet_directory_uri() . '/css/linen-calculator.css',
        array(),
        filemtime(get_stylesheet_directory() . '/css/linen-calculator.css')
    );

    // JS
$script_handle = 'linen-calculator-js'; // Use your script handle

wp_enqueue_script(
    $script_handle,
    get_stylesheet_directory_uri() . '/js/linen-calculator.js', // CORRECTED TO MATCH YOUR PATH
    array('jquery'),
    filemtime(get_stylesheet_directory() . '/js/linen-calculator.js'), // CORRECTED HERE TOO
    true
);
   
// Pass AJAX URL and nonce for secure AJAX requests
wp_localize_script(
    $script_handle,
    'ajax_object',
    array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'add_calculator_items_nonce' )
    )
);
}
add_action('wp_enqueue_scripts', 'linen_calculator_assets');


// =========================================================================
// CALCULATOR SHORTCODE (Displays the Table)
// =========================================================================

/**
 * Custom Daily Hire Calculator Shortcode Handler.
 */
function custom_daily_hire_calculator_shortcode() {
    // Ensure WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        return '<p>WooCommerce is required for this calculator.</p>';
    }

    ob_start();

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1, 
        'status'         => 'publish'
    );

    $products = new WP_Query( $args );

    if ( $products->have_posts() ) :
    ?>
    <div id="daily-hire-calculator-container">
        <div class="calculator-header">
            <span>ITEM</span>
            <span>PICK UP PRICE</span>
            <span>DAILY HIRE</span>
            <span>QUANTITY</span>
            <span>DAYS TO HIRE</span>
            <span>COST</span>
        </div>
        
        <?php 
        while ( $products->have_posts() ) : $products->the_post();
            global $product;
            $product_id = $product->get_id();
            
            // Retrieve ACF fields
            $pickup_price     = get_field( 'pickup_price', $product_id );
            $daily_hire_price = get_field( 'daily_hire_price', $product_id );

            // Skip if custom prices are not set
            if ( empty($pickup_price) || empty($daily_hire_price) ) {
                continue;
            }
        ?>
            <div class="calculator-row" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                <span class="item-name"><?php the_title(); ?></span>
                <span class="pickup-price" data-price="<?php echo esc_attr( $pickup_price ); ?>"><?php echo wc_price( $pickup_price ); ?></span>
                <span class="daily-hire-price" data-daily-rate="<?php echo esc_attr( $daily_hire_price ); ?>"><?php echo wc_price( $daily_hire_price ); ?>/day</span>
                
                <div class="input-group">
                    <button class="quantity-minus">-</button>
                    <input type="number" class="item-quantity" value="0" min="0">
                    <button class="quantity-plus">+</button>
                </div>
                
                <div class="input-group">
                    <button class="days-minus">-</button>
                    <input type="number" class="days-to-hire" value="0" min="0">
                    <button class="days-plus">+</button>
                </div>
                
                <span class="item-cost">$0.00</span>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>

        <div class="calculator-footer">
            <span id="total-cost">Total Cost: $0.00</span>
            <button id="add-to-cart-button">Add to Cart</button>
        </div>
    </div>
    <?php
    else :
        echo '<p>No hire products found.</p>';
    endif;

    return ob_get_clean();
}
add_shortcode( 'daily_hire_calculator', 'custom_daily_hire_calculator_shortcode' );


// =========================================================================
// WOOCOMMERCE AJAX AND PRICING LOGIC
// =========================================================================

/**
 * AJAX handler for adding calculator items to WooCommerce cart.
 */
function add_calculator_items_to_cart_callback() {

    // Verify nonce
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'add_calculator_items_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce.' ) );
        return;
    }

    if ( ! isset( $_POST['items'] ) || ! class_exists( 'WooCommerce' ) ) {
        wp_send_json_error( array( 'message' => 'Invalid request or WooCommerce not active.' ) );
        return;
    }

    $items = (array) $_POST['items'];
    
    // Clear cart to ensure a fresh, consistent booking
    WC()->cart->empty_cart();
    
    // Get the consistent days_to_hire value from the first item
    $days_to_hire = intval( $items[0]['days_to_hire'] ); 

    foreach ( $items as $item ) {
        $product_id = intval( $item['product_id'] );
        $quantity = intval( $item['quantity'] );
        
        // Pass custom cart data
        $cart_item_data = array(
            'days_to_hire' => $days_to_hire,
            'is_hire_item' => true,
        );

        WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_data );
    }

    // Redirect to the checkout page
    wp_send_json_success( array( 
        'redirect_url' => wc_get_checkout_url() 
    ) );
}
add_action( 'wp_ajax_add_calculator_items_to_cart', 'add_calculator_items_to_cart_callback' );
add_action( 'wp_ajax_nopriv_add_calculator_items_to_cart', 'add_calculator_items_to_cart_callback' );


/**
 * Override item price with the custom calculated price using ACF data.
 * This MUST use the same custom formula as the JS calculator (Formula C)
 */
function set_custom_price_in_cart( $cart_object ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }

    foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
        
        if ( isset( $cart_item['is_hire_item'] ) && $cart_item['is_hire_item'] ) {
            $product_id = $cart_item['product_id'];
            $days_to_hire = $cart_item['days_to_hire'];
            $quantity = $cart_item['quantity'];
            
            // Retrieve ACF fields
            $pickup_price     = floatval( get_field( 'pickup_price', $product_id ) );
            $daily_hire_price = floatval( get_field( 'daily_hire_price', $product_id ) );

            // --- IMPORTANT: APPLY FORMULA C ---
            // Formula C: (Pick Up Price * Quantity) + (Daily Hire Price * Days to Hire)
            $total_pickup_cost = $pickup_price * $quantity;
            $total_daily_hire_cost = $daily_hire_price * $days_to_hire; 
            
            // The final price shown by WooCommerce is the price per UNIT.
            // Therefore, we calculate the total cost for the item and then divide by quantity.
            $total_item_cost = $total_pickup_cost + $total_daily_hire_cost;
            
            // This ensures the unit price reflects the *total* cost divided by the quantity.
            // E.g., if total cost is $2.70 and Qty is 2, unit price must be $1.35.
            $item_unit_price = $total_item_cost / $quantity;

            $cart_item['data']->set_price( $item_unit_price );
        }
    }
}
add_action( 'woocommerce_before_calculate_totals', 'set_custom_price_in_cart', 20, 1 );


/**
 * Display the custom data (Days to Hire) in the cart and checkout items.
 */
function display_days_to_hire_cart_item_data( $item_data, $cart_item ) {
    if ( isset( $cart_item['days_to_hire'] ) && $cart_item['days_to_hire'] > 0) {
        $item_data[] = array(
            'name'  => __( 'Days to Hire', 'text-domain' ),
            'value' => $cart_item['days_to_hire'],
        );
    }
    return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'display_days_to_hire_cart_item_data', 10, 2 );


// =========================================================================
// CHECKOUT FIELD CUSTOMIZATION
// =========================================================================

/**
 * Add Delivery Date, Delivery Time, Pickup Date (auto) and hidden Days to Hire field.
 */
add_filter('woocommerce_checkout_fields', 'linen_checkout_fields');
function linen_checkout_fields($fields) {

    // --- DELIVERY DATE ---
    $fields['billing']['delivery_date'] = array(
        'type'        => 'date',
        'label'       => __('Delivery Date', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-first'),
        'priority'    => 20,
    );

    // --- DELIVERY TIME ---
    $fields['billing']['delivery_time'] = array(
        'type'        => 'select',
        'label'       => __('Delivery Time', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-last'),
        'priority'    => 21,
        'options'     => array(
            ''      => 'Select time',
            '10:00' => '10:00 AM',
            '11:00' => '11:00 AM',
            '12:00' => '12:00 PM',
            '13:00' => '1:00 PM',
            '14:00' => '2:00 PM',
            '15:00' => '3:00 PM',
            '16:00' => '4:00 PM',
            '17:00' => '5:00 PM',
            '18:00' => '6:00 PM',
            '19:00' => '7:00 PM',
            '20:00' => '8:00 PM',
            '21:00' => '9:00 PM',
            '22:00' => '10:00 PM',
        ),
    );

    // --- PICKUP DATE (AUTO-CALCULATED, READONLY) ---
    $fields['billing']['pickup_date'] = array(
    'type'        => 'text',
    'label'       => __('Pickup Date', 'woocommerce'),
    'required'    => false,
    'class'       => array('form-row-wide'),
    'priority'    => 22,
    'custom_attributes' => array('readonly' => 'readonly'),
);


    // --- DAYS TO HIRE HIDDEN FIELD ---
    if (!WC()->cart->is_empty()) {
        $first_item = reset(WC()->cart->get_cart());
        $days_to_hire = isset($first_item['days_to_hire']) ? $first_item['days_to_hire'] : 0;

        $fields['billing']['hire_info_days'] = array(
            'type'    => 'hidden',
            'default' => $days_to_hire,
            'priority' => 23,
        );
    }

    return $fields;
}

/**
 * Save Delivery Date, Delivery Time and Pickup Date into Order Meta.
 */
add_action('woocommerce_checkout_update_order_meta', 'linen_save_checkout_fields');
function linen_save_checkout_fields($order_id) {

    if (isset($_POST['delivery_date'])) {
        update_post_meta($order_id, '_delivery_date', sanitize_text_field($_POST['delivery_date']));
    }

    if (isset($_POST['delivery_time'])) {
        update_post_meta($order_id, '_delivery_time', sanitize_text_field($_POST['delivery_time']));
    }

    if (isset($_POST['pickup_date'])) {
        update_post_meta($order_id, '_pickup_date', sanitize_text_field($_POST['pickup_date']));
    }
}

/**
 * Pass Days to Hire from Cart → JS on Checkout
 */
add_action('wp_enqueue_scripts', function () {
    if (is_checkout() && !is_wc_endpoint_url()) {

        if (!WC()->cart->is_empty()) {
            $first_item = reset(WC()->cart->get_cart());
            $days_to_hire = isset($first_item['days_to_hire']) ? $first_item['days_to_hire'] : 0;

            wp_localize_script(
                'linen-calculator-js',
                'hire_checkout_data',
                array(
                    'days_to_hire' => $days_to_hire
                )
            );
        }
    }
});

add_filter( 'woocommerce_checkout_fields', 'ds_fix_pickup_date_type' );
function ds_fix_pickup_date_type( $fields ) {
    if ( isset( $fields['billing']['pickup_date'] ) ) {

        // Change field type to date
        $fields['billing']['pickup_date']['type'] = 'date';

        // Remove readonly
        unset( $fields['billing']['pickup_date']['custom_attributes']['readonly'] );
    }
    return $fields;
}

// Load JS file for checkout hire custom fields
function hire_checkout_assets() {
    if (is_checkout() && !is_wc_endpoint_url()) {
        wp_enqueue_script(
            'hire-checkout-js',
            get_stylesheet_directory_uri() . '/js/hire-checkout.js',
            array('jquery'),
            filemtime(get_stylesheet_directory() . '/js/hire-checkout.js'),
            true
        );

        // Pass Days to Hire from Cart → JS
        if (!WC()->cart->is_empty()) {
            $first_item = reset(WC()->cart->get_cart());
            $days_to_hire = isset($first_item['days_to_hire']) ? $first_item['days_to_hire'] : 0;

            wp_localize_script(
                'hire-checkout-js',
                'hire_checkout_data',
                array(
                    'days_to_hire' => $days_to_hire
                )
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'hire_checkout_assets');

// Show remove link for each item on checkout page
add_filter( 'woocommerce_cart_item_name', 'add_remove_link_on_checkout', 10, 3 );
function add_remove_link_on_checkout( $product_name, $cart_item, $cart_item_key ) {
    if ( is_checkout() ) {

        $remove_link = sprintf(
            '<a href="%s" class="remove-item" aria-label="%s">&times;</a>',
            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
            __( 'Remove this item', 'woocommerce' )
        );

        $product_name .= ' ' . $remove_link;
    }

    return $product_name;
}

// Refresh checkout after cart fragment updates
add_action( 'wp_footer', 'refresh_checkout_on_item_removal' );
function refresh_checkout_on_item_removal() {
    if ( is_checkout() ) :
    ?>
    <script type="text/javascript">
    jQuery(function($){
        $('body').on('click', '.remove-item', function(){
            // Delay to allow WooCommerce AJAX to finish
            setTimeout(function(){
                $('body').trigger('update_checkout');
            }, 1000);
        });
    });
    </script>
    <?php
    endif;
}

add_filter( 'woocommerce_form_field', function( $field, $key, $args, $value ) {
    if ( $key === 'pickup_date' ) {
        $field = str_replace( '(optional)', '', $field );
    }
    return $field;
}, 10, 4 );


// Create a shortcode for WooCommerce cart icon with count
function custom_wc_cart_icon_shortcode() {
    ob_start();
    ?>
    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="custom-cart-link" style="position: relative;">
        <i class="fas fa-shopping-cart"></i>
        <span class="custom-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    </a>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_cart_icon', 'custom_wc_cart_icon_shortcode');

// Enable AJAX refresh for dynamic cart count
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    ob_start();
    ?>
    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="custom-cart-link">
        <i class="fas fa-shopping-cart"></i>
        <span class="custom-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    </a>
    <?php
    $fragments['.custom-cart-link'] = ob_get_clean();
    return $fragments;
});

?>
