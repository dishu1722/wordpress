jQuery(document).ready(function($) {

    var $calculatorContainer = $('#daily-hire-calculator-container');
    if (!$calculatorContainer.length) return;

    // --- GLOBAL: store the current synced days-to-hire ---
    var syncedDays = 0;

    // --- HELPER: calculate row cost ---
    function calculateRowCost($row) {
        var quantity = parseInt($row.find('.item-quantity').val()) || 0;
        var days = parseInt($row.find('.days-to-hire').val()) || 0;
        var pickupPrice = parseFloat($row.find('.pickup-price').data('price')) || 0;
        var dailyPrice = parseFloat($row.find('.daily-hire-price').data('daily-rate')) || 0;
        var total = quantity * pickupPrice + dailyPrice * days;
        $row.find('.item-cost').text('$' + total.toFixed(2));
        return total;
    }

    // --- HELPER: calculate total cost ---
    function calculateTotalCost() {
        var total = 0;
        $calculatorContainer.find('.calculator-row').each(function() {
            total += calculateRowCost($(this));
        });
        $('#total-cost').text('Total Cost: $' + total.toFixed(2));
    }

    // --- SYNC DAYS TO ALL ACTIVE ROWS ---
    function syncDaysToAllRows(days) {
        syncedDays = days; // update global synced value
        $calculatorContainer.find('.calculator-row').each(function() {
            var $row = $(this);
            var quantity = parseInt($row.find('.item-quantity').val()) || 0;
            if (quantity > 0) {
                $row.find('.days-to-hire').val(syncedDays);
            }
        });
        calculateTotalCost();
    }

    // --- BUTTON CLICK HANDLER ---
    $calculatorContainer.on('click', '.quantity-plus, .quantity-minus, .days-plus, .days-minus', function() {
        var $button = $(this);
        var $row = $button.closest('.calculator-row');
        var $input;

        if ($button.hasClass('quantity-plus') || $button.hasClass('quantity-minus')) {
            $input = $row.find('.item-quantity');
        } else {
            $input = $row.find('.days-to-hire');
        }

        var step = ($button.hasClass('quantity-minus') || $button.hasClass('days-minus')) ? -1 : 1;
        var min = parseInt($input.attr('min')) || 0;
        var current = parseInt($input.val()) || min;
        var newVal = Math.max(min, current + step);
        $input.val(newVal);

        if ($input.hasClass('days-to-hire')) {
            // days changed manually → sync all
            syncDaysToAllRows(newVal);
        } else {
            // quantity changed
            if (newVal > 0 && syncedDays > 0) {
                // propagate existing synced days to this new active row
                $row.find('.days-to-hire').val(syncedDays);
            } else if (newVal <= 0) {
                // quantity 0 → reset days
                $row.find('.days-to-hire').val(0);
            }
            calculateTotalCost();
        }
    });

    // --- INPUT CHANGE HANDLER ---
    $calculatorContainer.on('change', '.item-quantity, .days-to-hire', function() {
        var $row = $(this).closest('.calculator-row');
        var quantity = parseInt($row.find('.item-quantity').val()) || 0;
        var days = parseInt($row.find('.days-to-hire').val()) || 0;

        if (quantity <= 0) $row.find('.days-to-hire').val(0);

        if ($(this).hasClass('days-to-hire') && quantity > 0) {
            syncDaysToAllRows(days);
        } else if ($(this).hasClass('item-quantity') && quantity > 0) {
            // new row activated → set days to current synced value
            if (syncedDays > 0) {
                $row.find('.days-to-hire').val(syncedDays);
            }
        }
        calculateTotalCost();
    });

    // --- ADD TO CART AJAX ---
    $('#add-to-cart-button').on('click', function() {
    var itemsArray = [];

    $('.calculator-row').each(function() {
        var quantity = parseInt($(this).find('.item-quantity').val());
        var daysToHire = parseInt($(this).find('.days-to-hire').val());
        if (quantity > 0) {
            itemsArray.push({
                product_id: $(this).data('product-id'),
                quantity: quantity,
                days_to_hire: daysToHire
            });
        }
    });

    if (itemsArray.length === 0) {
        alert('Please select at least one item.');
        return;
    }

    $.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: {
            action: 'add_calculator_items_to_cart',
            items: itemsArray,
            _wpnonce: ajax_object.nonce 
        },
        success: function(response) {
            if (response.success) {
                window.location.href = response.data.redirect_url;
            } else {
                alert('Error adding items to cart.');
            }
        }
    });
});


    // --- INITIAL CALCULATION ---
    calculateTotalCost();

    // --- CHECKOUT DATE LOGIC ---
    if ($('body').hasClass('woocommerce-checkout')) {
        var $deliveryField = $('#billing_delivery_date_time');
        var daysToHire = parseInt($('#billing_hire_info_days').val()) || 0;


        $deliveryField.attr('type', 'date');

        $deliveryField.on('change', function() {
            var deliveryDateString = $(this).val();
            var $pickupInput = $('#calculated-pickup-date-input');

            if (deliveryDateString && daysToHire > 0) {
                var deliveryDate = new Date(deliveryDateString + 'T00:00:00'); 
                var pickupDate = new Date(deliveryDate);
                pickupDate.setDate(deliveryDate.getDate() + daysToHire); 

                var year = pickupDate.getFullYear();
                var month = ('0' + (pickupDate.getMonth() + 1)).slice(-2);
                var day = ('0' + pickupDate.getDate()).slice(-2);
                $pickupInput.val(year + '-' + month + '-' + day);
            } else {
                $pickupInput.val('Please select a Delivery Date.');
            }
        });
    }
    

// --- CHECKOUT PICKUP DATE AUTO CALC ---
jQuery(function($){

    // ONLY run on Checkout Page
    if (!$('body').hasClass('woocommerce-checkout')) return;

    // Days to Hire coming from PHP → JS
    var daysToHire = (window.hire_checkout_data && hire_checkout_data.days_to_hire)
        ? parseInt(hire_checkout_data.days_to_hire)
        : 0;

    // When the user changes the Delivery Date
    $('#delivery_date').on('change', function() {
        let deliveryDate = $(this).val(); // YYYY-MM-DD

        if (deliveryDate && daysToHire > 0) {

            // Convert to date object
            let d = new Date(deliveryDate);

            // Add days_to_hire
            d.setDate(d.getDate() + daysToHire);

            // Convert back to YYYY-MM-DD
            let pickupY = d.getFullYear();
            let pickupM = String(d.getMonth() + 1).padStart(2, '0');
            let pickupD = String(d.getDate()).padStart(2, '0');

            let pickupFormatted = `${pickupY}-${pickupM}-${pickupD}`;

            // Auto-fill pickup date
            $('#pickup_date').val(pickupFormatted);
        }
    });

    // Make pickup date unclickable
    $('#pickup_date').attr('readonly', true).on('click', function(e){
        e.preventDefault();
        return false;
    });
});
});
