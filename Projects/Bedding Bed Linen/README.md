# 🧺 Bedding & Bed Linen – Website Development (WordPress + WooCommerce + Custom Calculator)

**Live Project:** [https://beddingbedlinen.com.au](https://beddingbedlinen.com.au)  

This is a complete WordPress website developed using Elementor + Hello Elementor Child Theme, featuring a fully custom WooCommerce-based Linen Hire Calculator, a fully customized checkout experience, and complete site design across multiple pages.

---

## 🚀 Project Overview
This project was built for a linen hire business in Australia.  
The goal was to design a modern, clean website with a **custom hire calculator** that dynamically calculates cost based on:

- Quantity
- Pickup Price
- Daily Hire Rate
- Days to Hire

The system integrates directly with WooCommerce so users can add selected items to the cart and complete checkout with **custom pickup/delivery date fields**.

---

## 🛠️ Tech Stack
| Technology | Usage |
|------------|-------|
| WordPress | CMS |
| Hello Elementor Child Theme | Theme customization |
| Elementor (Pro + widgets) | Page design |
| WooCommerce | eCommerce platform |
| Custom PHP | functions.php for calculator & hooks |
| JavaScript / jQuery | Calculator logic, AJAX |
| CSS | Custom styling |
| MetForm | Contact forms |
| SiteGround SMTP | Email delivery |
| GitHub | Version control |

---

## 🎨 Website Pages Designed
| Page | Built Using | Notes |
|------|------------|-------|
| Home | Elementor | Full layout, hero section, services, CTAs |
| FAQ | Elementor | Styled FAQ accordion blocks |
| About | Elementor | Branding, business information |
| Cost Calculator | Custom + Elementor | Entire calculator built manually |
| Contact | Elementor + MetForm | SMTP configured, email working |

---

## 🧮 Custom Linen Hire Calculator (Major Feature)
- Fully custom UI (HTML/CSS/JS)
- Supports 100+ potential products
- Dynamic calculations per row:
  - Add/remove quantity
  - Change days to hire
  - Auto-updating total cost
- Mobile-responsive
- Floating "Add to Cart" footer bar
- Custom AJAX call to add selected items to WooCommerce cart
- Nonce security implemented
- Error handling included
- Products created in WooCommerce and connected to calculator

---

## 🛒 WooCommerce Customizations
### Checkout Page
- Added Delivery Date, Pickup Date, Time fields
- Pickup date auto-calculates based on selected days
- Disabled manual editing for Pickup Date
- “Optional” removed from fields
- Custom validation & styling

### Cart System
- Synced calculator with WooCommerce
- Custom cart icon with live count in header
- Error handling for invalid items
- Removed default purchase rules blocking calculator items

### Order Page
- Custom fields (delivery/pickup date & time) visible in order admin
- Passed all custom checkout data into WooCommerce order meta

---

## 📧 Contact Form + SMTP (MetForm)
- Built complete contact form
- Created SMTP email account
- Connected SMTP via SiteGround Email Tools
- Integrated MetForm for sending emails through SMTP
- Verified entry storage and email delivery

---

## 🗂️ Project Folder Structure (Recommended for GitHub)
# 🧺 Bedding & Bed Linen – Website Development (WordPress + WooCommerce + Custom Calculator)

**Live Project:** [https://beddingbedlinen.com.au](https://beddingbedlinen.com.au)  

This is a complete WordPress website developed using Elementor + Hello Elementor Child Theme, featuring a fully custom WooCommerce-based Linen Hire Calculator, a fully customized checkout experience, and complete site design across multiple pages.

---

## 🚀 Project Overview
This project was built for a linen hire business in Australia.  
The goal was to design a modern, clean website with a **custom hire calculator** that dynamically calculates cost based on:

- Quantity
- Pickup Price
- Daily Hire Rate
- Days to Hire

The system integrates directly with WooCommerce so users can add selected items to the cart and complete checkout with **custom pickup/delivery date fields**.

---

## 🛠️ Tech Stack
| Technology | Usage |
|------------|-------|
| WordPress | CMS |
| Hello Elementor Child Theme | Theme customization |
| Elementor (Pro + widgets) | Page design |
| WooCommerce | eCommerce platform |
| Custom PHP | functions.php for calculator & hooks |
| JavaScript / jQuery | Calculator logic, AJAX |
| CSS | Custom styling |
| MetForm | Contact forms |
| SiteGround SMTP | Email delivery |
| GitHub | Version control |

---

## 🎨 Website Pages Designed
| Page | Built Using | Notes |
|------|------------|-------|
| Home | Elementor | Full layout, hero section, services, CTAs |
| FAQ | Elementor | Styled FAQ accordion blocks |
| About | Elementor | Branding, business information |
| Cost Calculator | Custom + Elementor | Entire calculator built manually |
| Contact | Elementor + MetForm | SMTP configured, email working |

---

## 🧮 Custom Linen Hire Calculator (Major Feature)
- Fully custom UI (HTML/CSS/JS)
- Supports 100+ potential products
- Dynamic calculations per row:
  - Add/remove quantity
  - Change days to hire
  - Auto-updating total cost
- Mobile-responsive
- Floating "Add to Cart" footer bar
- Custom AJAX call to add selected items to WooCommerce cart
- Nonce security implemented
- Error handling included
- Products created in WooCommerce and connected to calculator

---

## 🛒 WooCommerce Customizations
### Checkout Page
- Added Delivery Date, Pickup Date, Time fields
- Pickup date auto-calculates based on selected days
- Disabled manual editing for Pickup Date
- “Optional” removed from fields
- Custom validation & styling

### Cart System
- Synced calculator with WooCommerce
- Custom cart icon with live count in header
- Error handling for invalid items
- Removed default purchase rules blocking calculator items

### Order Page
- Custom fields (delivery/pickup date & time) visible in order admin
- Passed all custom checkout data into WooCommerce order meta

---

## 📧 Contact Form + SMTP (MetForm)
- Built complete contact form
- Created SMTP email account
- Connected SMTP via SiteGround Email Tools
- Integrated MetForm for sending emails through SMTP
- Verified entry storage and email delivery

---

## 🗂️ Project Folder Structure (Recommended for GitHub)
Bedding Bed Linen/
├── themes/
 │    └── hello-elementor-child-theme/
 │         ├── functions.php
 │         ├── style.css
 │         ├── js/
 │         │    ├── linen-calculator.js
 │         │    ├── hire-checkout.js
 │         │    └── scripts.js
 │         └──  css/
 │               └── linen-calculator.css  
 │
 ├── screenshot-previews/
 │    ├── homepage.png
 │    ├── calculator.png
 │    ├── checkout.png
 │    └── mobile-view.png
 │
 ├── README.md
 └── .gitignore
 
---

## 📸 Screenshots
Homepage:  
![Homepage](screenshot-previews/homepage.png)

Calculator Section:  
![Calculator](screenshot-previews/calculator.png)

Mobile Calculator View:  
![Mobile View](screenshot-previews/mobile-view.png)

Checkout Page with Custom Fields:  
![Checkout](screenshot-previews/checkout.png)

Cart Icon with Counter:  
![Cart Icon](screenshot-previews/cart-icon.png)

---

## 🧑‍💻 My Role & Responsibilities
- Designed all main pages using Elementor
- Built entire Linen Hire Calculator system (PHP, JS, CSS)
- Developed custom PHP hooks, AJAX calls, and JS logic
- Setup WooCommerce products for calculator (pickup/daily hire)
- Customized Cart, Checkout, and Orders
- Created contact form and configured SMTP
- Performed testing, debugging, and optimization
- Made website responsive and mobile-friendly

---

## 📦 Setup Instructions (for reuse)
1. Install WordPress
2. Install the following plugins:
   - Hello Elementor theme
   - Elementor / Elementor Pro
   - WooCommerce
   - MetForm
3. Upload the child theme from this repo
4. Add WooCommerce products
5. Import all calculator JS/CSS
6. Add custom date/time fields to checkout
7. Configure SMTP for forms
8. Test cart/checkout flow

---

## 📄 License
This is a client project – code included for **portfolio purposes only**.
