=== HDWebmobile Shipment Tracking ===
Contributors: htrxuan
Donate link: https://paypal.me/htrxuan/20
Tags: woocommerce, shipment tracking, order tracking, shipping, carrier
Requires at least: 6.9
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
Requires Plugins: woocommerce
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a carrier and tracking number to any order and show it to the customer -- no bulk-import surface, strict output escaping.

== Description ==

HDWebmobile Shipment Tracking adds a simple "Shipment Tracking" section to the WooCommerce order edit screen. Pick a carrier, enter the tracking number, and the customer sees it on their order page (both the classic My Account view and the thank-you page) and receives a "Your order has shipped" email with a direct link to track their package.

Two real 2026 vulnerabilities were researched in this exact plugin category: a stored XSS in a competing "Shipment Tracker" plugin caused by unescaped tracking-number/carrier input, and a SQL injection in a competing plugin's CSV bulk-import feature. This plugin closes both by construction -- every tracking field is strictly allowlist-sanitized on save and escaped on every output, and there is no bulk-import feature at all in this version.

= Key Features =
* Add a carrier and tracking number directly on the order edit screen -- no separate settings page needed
* Predefined carrier list with automatic tracking-link generation: USPS, UPS, FedEx, DHL, GHN, GHTK, Vietnam Post, plus a custom-URL option for any other carrier
* Automatic "Your order has shipped" email the moment a tracking number is first added, with a manual resend option any time after
* Tracking shown on the customer's My Account order page and the order-received/thank-you page
* Strict allowlist sanitization on every field, escaped on every output -- closes the exact stored-XSS class found in a competing plugin
* Zero bulk-import surface -- closes the exact SQL-injection class found in a competing plugin's CSV import feature

= Limitations (please read before installing) =
* One tracking number per order -- no multi-package/partial-shipment support in this version
* No bulk CSV import -- a deliberate security tradeoff, not an oversight; see above
* No live carrier-API status polling ("in transit" / "delivered" webhooks) -- just a link to the carrier's own tracking page
* No tracking column on the My Account orders list -- tracking is shown on the individual order page only

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/hdwebmobile-shipment-tracking` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress. WooCommerce must already be installed and active.
3. Open any order's edit screen -- the Shipment Tracking section appears automatically, no configuration needed.

== How to Use ==

= 1. Add tracking to an order =
Open an order's edit screen (Screenshot 1), scroll to the "Shipment Tracking" section, choose a carrier, enter the tracking number, and click Update.

= 2. The customer is notified automatically =
The first time a tracking number is saved for an order, a "Your order has shipped" email (Screenshot 2) is sent automatically with a direct tracking link.

= 3. The customer can see it anytime =
The tracking info also appears on the customer's My Account order page (Screenshot 3) and the order-received/thank-you page, for as long as they're logged in or hold the order key.

= 4. Resend the notification =
If you need to resend the shipped email (e.g. the customer says they never got it), click "Resend shipping notification email" on the order edit screen -- no need to re-enter the tracking number.

== Screenshots ==

1. The Shipment Tracking fields on the admin order edit screen.
2. The "Your order has shipped" email.
3. The Shipping section on the customer's My Account order page.

== Changelog ==

= 1.0.0 =
* Initial release: carrier + tracking number on the order edit screen, automatic shipped-notification email, customer-facing tracking display, strict input sanitization and output escaping, no bulk-import surface.
