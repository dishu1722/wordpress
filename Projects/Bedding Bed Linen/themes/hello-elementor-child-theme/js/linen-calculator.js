jQuery(document).ready(function($) {
    var $calculatorContainer = $('#daily-hire-calculator-container');
    if (!$calculatorContainer.length) return;

    // Global synced days value (0 means "not set yet")
    var syncedDays = 0;

    // Helper: calculate a row cost
    function calculateRowCost($row) {
        var quantity = parseInt($row.find('.item-quantity').val()) || 0;
        var days = parseInt($row.find('.days-to-hire').val()) || 0;
        var pickupPrice = parseFloat($row.find('.pickup-price').data('price')) || 0;
        var dailyPrice = parseFloat($row.find('.daily-hire-price').data('daily-rate')) || 0;
        var total = quantity * pickupPrice + dailyPrice * days;
        $row.find('.item-cost').text('$' + total.toFixed(2));
        return total;
    }

    // Helper: calculate total cost for all rows
    function calculateTotalCost() {
        var total = 0;
        $calculatorContainer.find('.calculator-row').each(function() {
            total += calculateRowCost($(this));
        });
        $('#total-cost').text('Total Cost: $' + total.toFixed(2));
    }

    // Helper: get highest days among selected rows (qty >= 1)
    function getHighestDaysAcrossSelectedRows() {
        var highest = 0;
        $calculatorContainer.find('.calculator-row').each(function() {
            var qty = parseInt($(this).find('.item-quantity').val()) || 0;
            var days = parseInt($(this).find('.days-to-hire').val()) || 0;
            if (qty > 0 && days > highest) highest = days;
        });
        return highest;
    }

    // Sync days to all rows that are selected (qty >= 1)
    function syncDaysToAllSelectedRows(days) {
        syncedDays = days;
        $calculatorContainer.find('.calculator-row').each(function() {
            var $row = $(this);
            var qty = parseInt($row.find('.item-quantity').val()) || 0;
            if (qty > 0) {
                $row.find('.days-to-hire').val(syncedDays);
            }
        });
        calculateTotalCost();
    }

    // When quantity changes from 0 -> >0, decide what days to set on that row
    function applyDaysToNewlyActivatedRow($row) {
        var currentDays = parseInt($row.find('.days-to-hire').val()) || 0;

        if (syncedDays > 0) {
            // global days already set — use it
            $row.find('.days-to-hire').val(syncedDays);
        } else if (currentDays > 0) {
            // row already had days (rare), set global
            syncedDays = currentDays;
            syncDaysToAllSelectedRows(syncedDays);
        } else {
            // no global days yet and row days is 0 → default to 1
            syncedDays = 1;
            $row.find('.days-to-hire').val(1);
        }
    }

    // --- BUTTON CLICK HANDLER (quantity + days buttons) ---
    $calculatorContainer.on('click', '.quantity-plus, .quantity-minus, .days-plus, .days-minus', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $row = $button.closest('.calculator-row');
        var $quantityInput = $row.find('.item-quantity');
        var $daysInput = $row.find('.days-to-hire');

        var isQuantityBtn = $button.hasClass('quantity-plus') || $button.hasClass('quantity-minus');
        var isDaysBtn = !isQuantityBtn;

        var $input = isQuantityBtn ? $quantityInput : $daysInput;
        var step = (isQuantityBtn && $button.hasClass('quantity-minus')) || (isDaysBtn && $button.hasClass('days-minus')) ? -1 : 1;
        var min = parseInt($input.attr('min')) || 0;
        var current = parseInt($input.val()) || 0;
        var newVal = Math.max(min, current + step);
        $input.val(newVal);

        if (isQuantityBtn) {
            // Quantity changed
            if (newVal <= 0) {
                // If quantity set to 0, reset days for that row to 0
                $daysInput.val(0);
            } else {
                // Row activated (was 0 -> >0) or quantity changed while active
                // If row was previously inactive (current === 0) we need to apply days
                if (current === 0) {
                    applyDaysToNewlyActivatedRow($row);
                } else {
                    // if already active, ensure its days follow syncedDays (if any)
                    if (syncedDays > 0) {
                        $daysInput.val(syncedDays);
                    } else if (parseInt($daysInput.val()) === 0) {
                        $daysInput.val(1);
                        syncedDays = 1;
                    }
                }
            }

            // After quantity change recompute highest days (defensive)
            var highest = getHighestDaysAcrossSelectedRows();
            if (highest > syncedDays) syncedDays = highest;

            calculateTotalCost();
            } else {
            // Days changed via buttons
            var qty = parseInt($quantityInput.val()) || 0;

            // If days clicked first and qty is 0 → set qty to 1 per rule
            if (qty === 0) {
                $quantityInput.val(1);
                qty = 1;
            }

            var daysVal = parseInt($daysInput.val()) || 0;

            // NEW RULE: If user sets days to 0 → reset quantity and cost
            if (daysVal === 0) {
                $quantityInput.val(0);
                calculateRowCost($row); 
                calculateTotalCost();
                return;
            }

            // Update global syncedDays and sync to all selected rows
            if (daysVal > 0) {
                syncedDays = daysVal;
                syncDaysToAllSelectedRows(syncedDays);
            } else {
                // if daysVal is 0, don't sync; keep 0 for unselected rows
                calculateTotalCost();
            }
        }
    });

    // --- MANUAL INPUT CHANGE HANDLER (typing in inputs) ---
    $calculatorContainer.on('change', '.item-quantity, .days-to-hire', function() {
        var $row = $(this).closest('.calculator-row');
        var $quantityInput = $row.find('.item-quantity');
        var $daysInput = $row.find('.days-to-hire');

        var quantity = parseInt($quantityInput.val()) || 0;
        var days = parseInt($daysInput.val()) || 0;

        // Rule: if quantity > 0 and days == 0 → set days = 1
        if (quantity > 0 && days === 0) {
            $daysInput.val(1);
            days = 1;
        }

        // Rule: if days > 0 and quantity == 0 → set quantity = 1 (user set days first)
        if (days > 0 && quantity === 0) {
            $quantityInput.val(1);
            quantity = 1;
        }

        // If days changed and row is selected, update global and sync
        if ($(this).hasClass('days-to-hire')) {
        
        // NEW RULE: If user manually types days = 0 → set qty = 0 and cost = 0
    if (days === 0) {
        $quantityInput.val(0);
        calculateRowCost($row);
        calculateTotalCost();
        return;
    }

        
            if (days > 0) {
                syncedDays = days;
                syncDaysToAllSelectedRows(syncedDays);
                return; // already recalculated totals in sync
            }
        }

        // If quantity changed and row just activated, apply days rules
        if ($(this).hasClass('item-quantity')) {
            if (quantity > 0 && (syncedDays > 0)) {
                $daysInput.val(syncedDays);
            } else if (quantity > 0 && syncedDays === 0 && parseInt($daysInput.val()) === 0) {
                // first activation without syncedDays -> set 1
                $daysInput.val(1);
                syncedDays = 1;
            } else if (quantity === 0) {
                $daysInput.val(0);
            }
        }

        calculateTotalCost();
    });

    // --- ADD TO CART AJAX: ensure days_to_hire is taken from active rows and uses syncedDays if needed ---
    $('#add-to-cart-button').on('click', function(e) {
        e.preventDefault();

        var cartItems = [];
        // Use final syncedDays if defined otherwise default to 1 for active items
        var finalDays = syncedDays || 1;

        $calculatorContainer.find('.calculator-row').each(function() {
            var $r = $(this);
            var qty = parseInt($r.find('.item-quantity').val()) || 0;
            var days = parseInt($r.find('.days-to-hire').val()) || 0;
            if (qty > 0) {
                // prefer the row's days if >0, otherwise use finalDays
                var useDays = days > 0 ? days : finalDays;
                cartItems.push({
                    product_id: $r.data('product-id'),
                    quantity: qty,
                    days_to_hire: useDays
                });
            }
        });

        if (cartItems.length === 0) {
            alert('Please select at least one item.');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Adding...');

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'add_calculator_items_to_cart',
                items: cartItems,
                _wpnonce: ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect_url;
                } else {
                    alert('Error adding items to cart.');
                    $btn.prop('disabled', false).text('Add to Cart');
                }
            },
            error: function() {
                alert('Network error.');
                $btn.prop('disabled', false).text('Add to Cart');
            }
        });
    });

    // initial calculation
    calculateTotalCost();
});
