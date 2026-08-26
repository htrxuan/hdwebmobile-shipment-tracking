<?php

/**
 * Order shipped email (plain text).
 *
 * @var \WC_Order $order
 * @var string $email_heading
 * @var string $additional_content
 * @var string $carrier
 * @var string $tracking_number
 * @var string $tracking_url
 */

if (!defined('ABSPATH')) {
    exit;
}

echo esc_html(wp_strip_all_tags($email_heading)) . "\n\n";

printf(
    /* translators: %s: order number */
    esc_html__('Good news -- your order #%s is on its way.', 'hdwebmobile-shipment-tracking'),
    esc_html($order->get_order_number())
);
echo "\n\n";

if ($carrier) {
    esc_html_e('Carrier:', 'hdwebmobile-shipment-tracking');
    echo ' ' . esc_html($carrier) . "\n";
}

esc_html_e('Tracking Number:', 'hdwebmobile-shipment-tracking');
echo ' ' . esc_html($tracking_number) . "\n\n";

if ($tracking_url) {
    esc_html_e('Track your package:', 'hdwebmobile-shipment-tracking');
    echo "\n" . esc_html($tracking_url) . "\n\n";
}

if ($additional_content) {
    echo esc_html(wp_strip_all_tags($additional_content)) . "\n";
}
