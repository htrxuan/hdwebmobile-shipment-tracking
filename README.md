# HDWebmobile Shipment Tracking

Add a carrier and tracking number to any order and show it to the customer -- no bulk-import surface, strict output escaping.

- **WordPress.org:** https://wordpress.org/plugins/hdwebmobile-shipment-tracking/
- **Requires:** WordPress 6.9+, WooCommerce, PHP 7.4+
- **License:** GPLv2 or later

## Description

HDWebmobile Shipment Tracking adds a simple "Shipment Tracking" section to the WooCommerce order edit screen. Pick a carrier, enter the tracking number, and the customer sees it on their order page (both the classic My Account view and the thank-you page) and receives a "Your order has shipped" email with a direct link to track their package.

Two real 2026 vulnerabilities were researched in this exact plugin category: a stored XSS in a competing "Shipment Tracker" plugin caused by unescaped tracking-number/carrier input, and a SQL injection in a competing plugin's CSV bulk-import feature. This plugin closes both by construction -- every tracking field is strictly allowlist-sanitized on save and escaped on every output, and there is no bulk-import feature at all in this version.

## Features

* Add a carrier and tracking number directly on the order edit screen -- no separate settings page needed
* Predefined carrier list with automatic tracking-link generation: USPS, UPS, FedEx, DHL, GHN, GHTK, Vietnam Post, plus a custom-URL option for any other carrier
* Automatic "Your order has shipped" email the moment a tracking number is first added, with a manual resend option any time after
* Tracking shown on the customer's My Account order page and the order-received/thank-you page
* Strict allowlist sanitization on every field, escaped on every output -- closes the exact stored-XSS class found in a competing plugin
* Zero bulk-import surface -- closes the exact SQL-injection class found in a competing plugin's CSV import feature

## Development

Standard WordPress plugin structure:

```
hdwebmobile-shipment-tracking.php    Bootstrap
includes/class-hdst-activator.php
includes/class-hdst-admin.php
includes/class-hdst-carriers.php
includes/class-hdst-core.php
includes/class-hdst-display.php
includes/class-hdst-email.php
includes/class-hdst-hub.php
```

Part of the [HDWebmobile](https://hdwebmobile.com/plugins/) suite of focused, single-purpose WooCommerce plugins.

## License

GPLv2 or later. See [LICENSE](LICENSE).

