// Adding a 'View cart' button next to the 'Add to Cart' button
document.addEventListener('DOMContentLoaded', function() {
    const cartForm = document.querySelector('.single-product form.cart');
    if(cartForm) {
        const viewCartBtn = document.createElement('a');
        viewCartBtn.href = wc_add_to_cart_params.cart_url; // WooCommerce cart URL
        viewCartBtn.className = 'view-cart-button';
        viewCartBtn.style.marginLeft = '10px';
        viewCartBtn.textContent = 'View Cart';
        cartForm.appendChild(viewCartBtn);
    }
});

//Adding 'Cart icon' li in the header desktop only
document.addEventListener("DOMContentLoaded", function() {
    let cartHTML = document.querySelector('#custom-cart-fragment').innerHTML;
    let menu = document.querySelector('.header-style nav ul'); // Change to your theme's menu selector
    if (menu) {
        let li = document.createElement('li');
        li.className = 'menu-item custom-menu-cart';
        li.innerHTML = cartHTML;
        menu.appendChild(li);
    }
});

// Code for both desktop and Mobile to show the Cart icon
document.addEventListener("DOMContentLoaded", function() {
    let cartHTML = document.querySelector('#custom-cart-fragment')?.innerHTML;
    if (!cartHTML) return; // Stop if no cart HTML found

    // Add to desktop menu (inside <ul>)
    let desktopMenu = document.querySelector('.header-style nav ul'); // Change selector if needed
    if (desktopMenu) {
        let li = document.createElement('li');
        li.className = 'menu-item custom-menu-cart';
        li.innerHTML = cartHTML;
        desktopMenu.appendChild(li);
    }

    // Add to mobile header (inside #mob-header)
    let mobileHeader = document.querySelector('#mob-header');
    if (mobileHeader) {
        let div = document.createElement('div');
        div.className = 'custom-menu-cart-mobile';
        div.innerHTML = cartHTML;
        mobileHeader.appendChild(div);
    }
});
