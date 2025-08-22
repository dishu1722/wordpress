//	Ordering the product category by Ascending order
jQuery(document).ready(function($) {
 	const container = $('.dipl_woo_products_category_layout.layout1');
    const items = container.find('.dipl_woo_products_category_isotope_item');
    const sortedItems = items.get().sort(function(a, b) {
       const nameA = $(a).find('.dipl_woo_product_category_name a').text().toUpperCase();
       const nameB = $(b).find('.dipl_woo_product_category_name a').text().toUpperCase();
        return nameA < nameB ? 1 : -1;
    });
    container.empty().append(sortedItems);	
	$('.product-select-bar').css('visibility', 'visible');  //Make this div visibility hidden in css also
});
