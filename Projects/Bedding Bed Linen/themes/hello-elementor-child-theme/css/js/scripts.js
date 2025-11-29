jQuery(document).ready(function($) {

  // Adding class to make header sticky
    var header = $('.ekit-template-content-header'); // Change this selector to match your header element
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 100) { // When scrolled more than 50px
            header.addClass('scrolled');
        } else {
            header.removeClass('scrolled');
        }
    });
});
