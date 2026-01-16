<?php
/*
 * This is the child theme for Bricks theme, generated with Generate Child Theme plugin by catchthemes.
 *
 * (Please see https://developer.wordpress.org/themes/advanced-topics/child-themes/#how-to-create-a-child-theme)
 */
add_action( 'wp_enqueue_scripts', 'bricks_child_enqueue_styles' );
function bricks_child_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
}
/*
 * Your code goes below
 */
/* Output term descriptions as JS object for the current product */
// Pass term descriptions to JS for swatches
add_action('wp_footer', function() {
    if (!is_product()) return;

    global $product;
    if (!$product) return;

    $attributes = $product->get_attributes();
    $term_descriptions = [];

    foreach ($attributes as $attr_name => $attr_obj) {
        if ($attr_obj->is_taxonomy()) {
            $taxonomy = $attr_obj->get_name(); // e.g., pa_sedak
            $terms = wp_get_post_terms($product->get_id(), $taxonomy, array('fields' => 'all'));
            foreach ($terms as $term) {
                // key: taxonomy:term_slug => value: term description
                $term_descriptions[$taxonomy . ':' . $term->slug] = strip_tags($term->description);
            }
        }
    }
    ?>
    <script>
        const termDescriptions = <?php echo wp_json_encode($term_descriptions); ?>;
    </script>
    <?php
});

// Show product add-ons below swatches
add_action('woocommerce_after_variations_form', function() {
    if (!is_product()) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the variations table tbody
        const tbody = document.querySelector('table.variations tbody');
        if (!tbody) return;

        // Create new table row
        const tr = document.createElement('tr');
        tr.classList.add('product-addon-row');

        // Label cell
        const th = document.createElement('th');
        th.className = 'label';
        th.textContent = 'Doplnky:';  // Your label
        tr.appendChild(th);

        // Value cell
        const td = document.createElement('td');
        td.className = 'value';
        td.innerHTML = `
            <label style="display:flex; align-items:center; gap:10px;">
            <img src="https://spinergo.kinsta.cloud/wp-content/uploads/2026/01/9a95b236fabfc3c3ffa5725351b1947f75a1d8a1.png" alt="Silikónové kolieska" width="50">
            <span>Silikónové kolieska</span>
            <span style="margin-left:auto;">+ 5€</span>
            <input type="checkbox" name="addon_silikonove_kolieska" value="5">
        </label>
        `;
        tr.appendChild(td);

        // Append this row to tbody
        tbody.appendChild(tr);
    });
    </script>
    <?php
});

// Add add-on price to cart
/* 1. Save add-on when adding to cart */
add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id) {

    if (isset($_POST['addon_silikonove_kolieska'])) {
        $cart_item_data['addon_silikonove_kolieska'] = 5;
        $cart_item_data['unique_key'] = md5(microtime()); // prevent merge
    }

    return $cart_item_data;
}, 10, 2);


/* 2. Add price safely (NO double calculation) */
add_action('woocommerce_before_calculate_totals', function ($cart) {

    if (is_admin() && !defined('DOING_AJAX')) return;
    if (did_action('woocommerce_before_calculate_totals') >= 2) return;

    foreach ($cart->get_cart() as $cart_item) {

        if (!isset($cart_item['addon_silikonove_kolieska'])) continue;

        $product = $cart_item['data'];
        $addon   = (float) $cart_item['addon_silikonove_kolieska'];

        // Get base price INCLUDING tax
        $base_price = wc_get_price_including_tax($product, [
            'qty'   => 1,
            'price' => $product->get_regular_price()
        ]);

        // Set final price WITHOUT taxing addon
        $product->set_price($base_price + $addon);

        // Prevent WooCommerce from taxing this item again
        $product->set_tax_status('none');
    }
});



/* 3. Show add-on in cart & checkout */
add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {

    if (isset($cart_item['addon_silikonove_kolieska'])) {
        $item_data[] = [
            'name'  => 'Doplnky',
            'value' => 'Silikónové kolieska (+5€)'
        ];
    }

    return $item_data;
}, 10, 2);
