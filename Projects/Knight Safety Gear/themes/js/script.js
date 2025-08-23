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

	// 	Removing the "27 units" showing on all product pages
  $('.save-label').each(function() {
    let text = $(this).text();
    let cleaned = text.replace(/- *\$[\d,.]+ *\/Unit/, '');
    $(this).text(cleaned.trim());
	$(this).css('visibility', 'visible'); //Make this div visibility hidden in css also
  });
});


// Updating the discount value according to the value in the given table(Fetch & display)
document.addEventListener('DOMContentLoaded', function () {
  const discountCell = document.querySelector('.price-comparison tr.highlight td:nth-child(4)');
  const discountMatch = discountCell?.textContent.match(/\d+%/);
  const actualDiscount = discountMatch ? discountMatch[0] : null;

  function updateDiscountTextIfCaseSelected() {
    const caseButton = document.querySelector('.button-variable-item-case');
    const isCaseSelected = caseButton?.classList.contains('selected');

    if (actualDiscount && isCaseSelected) {
      document.querySelectorAll('.woocommerce-variation-description').forEach(descEl => {
        descEl.innerHTML = descEl.innerHTML.replace(
          /Enjoy a \d+% discount/, `Enjoy a ${actualDiscount} discount`);
      });
    }
  }

  // Initial check
  updateDiscountTextIfCaseSelected();

  // Re-check when variation changes
  jQuery(document).on('found_variation', function () {
    setTimeout(updateDiscountTextIfCaseSelected, 300);
  });

  // Re-check when user clicks the Case tab
  const caseButton = document.querySelector('.button-variable-item-case');
  caseButton?.addEventListener('click', function () {
    setTimeout(updateDiscountTextIfCaseSelected, 300);
  });
});
