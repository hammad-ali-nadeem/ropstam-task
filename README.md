Ropstam Senior CMS Developer Assignment

This repository contains my implementations for the WordPress and Shopify tasks for the Senior CMS Developer position at Ropstam.

Repository Structure

ropstam-task/
 README.md
 shopify/
   bundle-builder.liquid
 wordpress/
    ropstam-service-addon/
        ropstam-service-addon.php
        includes/
        assets/





 1. WordPress — Service Add-on Calculator

Location: `wordpress/ropstam-service-addon/`

 Features

- Registers and manages Services with configurable base prices and delivery times.
- Supports service-specific metadata.
- Displays the three most recently updated services through the implemented frontend component.
- Provides a client-side calculator that updates the estimated price when optional add-ons are selected.
- Uses WordPress APIs and follows sanitization and output-escaping practices.

 Installation

1. Set up a working WordPress installation with PHP and a MySQL-compatible database.
2. Copy the complete `ropstam-service-addon` directory into:

   `wp-content/plugins/ropstam-service-addon/`

3. Open WordPress Admin → Plugins.
4. Activate the Service Add-on plugin.
5. Configure or create Services and set their base prices and delivery times.
6. Add the plugin's implemented shortcode or frontend component to a page, using the shortcode or usage instructions provided in the plugin code.
7. Open that page to test the service display, add-on selections, and estimated price calculation.

Testing Checklist

- Create Services with different base prices and delivery times.
- Confirm the three most recently updated Services are displayed.
- Select and deselect optional add-ons.
- Verify the estimated price updates correctly.
- Check the page on desktop and mobile layouts.



 2. Shopify — Online Store 2.0 Bundle Builder

Location: `shopify/bundle-builder.liquid`

Features

- Provides a configurable three-item bundle builder.
- Allows merchants to configure up to three product groups using the supported collection or product-list settings.
- Allows customers to select one product from each group.
- Updates the displayed bundle price as selections change.
- Adds the selected products to the cart using Shopify's Ajax Cart API without a full-page reload.
- Associates bundle items using line-item properties.
- Handles missing selections, sold-out products, loading states, and errors.

Installation

1. Open your Shopify store's admin dashboard.
2. Navigate to Online Store → Themes.
3. Duplicate your theme or use an unpublished development theme.
4. Open the theme's code editor.
5. In the `sections` directory, create a file named `bundle-builder.liquid`.
6. Copy the complete contents of this repository's `shopify/bundle-builder.liquid` into that file.
7. Save the file.
8. Open the theme editor for the unpublished theme.
9. Add the Bundle Builder section to a compatible page template, where the section is available.
10. Configure the product groups and products in the section settings.
11. Preview the page and test product selection, price calculation, cart additions, and sold-out handling.

Important: Do not publish changes to a live theme until testing is complete.

Testing Checklist

- Configure three product groups.
- Select one available product from each group.
- Verify the displayed price changes with the selected products.
- Attempt to add the bundle to the cart.
- Confirm all three selected products appear in the cart with their bundle properties.
- Test incomplete selections and sold-out products.
- Confirm loading and error states work without a page reload.

Author

Hammad Ali Nadeem

Assignment

Ropstam — Senior CMS Developer

