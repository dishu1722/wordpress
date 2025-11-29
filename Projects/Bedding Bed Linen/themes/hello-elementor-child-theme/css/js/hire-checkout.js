jQuery(document).ready(function($) {

    // Only run on checkout page
    if ($('body').hasClass('woocommerce-checkout')) {

        // Get days to hire from localized script
        var daysToHire = (window.hire_checkout_data && hire_checkout_data.days_to_hire)
            ? parseInt(hire_checkout_data.days_to_hire)
            : 0;

        // Make pickup date readonly
        $('#pickup_date').attr('readonly', true);

        // Prevent manual change of pickup date
        $('#pickup_date').on('click keydown', function(e){
            e.preventDefault();
            return false;
        });

        // Update pickup date when delivery date changes
        $('#delivery_date').on('change', function() {
            var deliveryDate = $(this).val();
            if (deliveryDate && daysToHire > 0) {
                var d = new Date(deliveryDate);
                d.setDate(d.getDate() + daysToHire);
                var pickupFormatted = d.toISOString().split('T')[0];
                $('#pickup_date').val(pickupFormatted);
            }
        });

        // Initialize pickup date on page load
        var initialDeliveryDate = $('#delivery_date').val();
        if (!initialDeliveryDate) {
          // If delivery date is empty, set it to today
          var today = new Date();
          var dd = String(today.getDate()).padStart(2, '0');
          var mm = String(today.getMonth() + 1).padStart(2, '0');
          var yyyy = today.getFullYear();
          initialDeliveryDate = yyyy + '-' + mm + '-' + dd;
          $('#delivery_date').val(initialDeliveryDate);
      }

        // Set pickup date based on delivery date + daysToHire
        if (initialDeliveryDate && daysToHire > 0) {
            var d = new Date(initialDeliveryDate);
            d.setDate(d.getDate() + daysToHire);
            var pickupFormatted = d.toISOString().split('T')[0];
            $('#pickup_date').val(pickupFormatted);
        }
    }
});
