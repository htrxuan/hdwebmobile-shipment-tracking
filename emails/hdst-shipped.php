<?php

/**
 * Order shipped email (HTML).
 *
 * @var \WC_Order $order
 * @var string $email_heading
 * @var string $additional_content
 * @var string $carrier
 * @var string $tracking_number
 * @var string $tracking_url
 * @var bool $sent_to_admin
 * @var bool $plain_text
 * @var \htrxuan\hdst\HDST_Email $email
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_email_header', $email_heading, $email); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, not ours to prefix
?>

<p>
    <?php
    printf(
        /* translators: %s: order number */
        esc_html__('Good news — your order #%s is on its way.', 'hdwebmobile-shipment-tracking'),
        esc_html($order->get_order_number())
    );
    ?>
</p>

<?php if ($carrier) : ?>
    <p><strong><?php esc_html_e('Carrier:', 'hdwebmobile-shipment-tracking'); ?></strong> <?php echo esc_html($carrier); ?></p>
<?php endif; ?>

<p><strong><?php esc_html_e('Tracking Number:', 'hdwebmobile-shipment-tracking'); ?></strong> <?php echo esc_html($tracking_number); ?></p>

<?php if ($tracking_url) : ?>
    <p style="text-align: center; margin: 24px 0;">
        <a href="<?php echo esc_url($tracking_url); ?>" class="button" style="background-color:#05A67D;color:#ffffff;padding:12px 24px;text-decoration:none;border-radius:4px;display:inline-block;">
            <?php esc_html_e('Track Your Package', 'hdwebmobile-shipment-tracking'); ?>
        </a>
    </p>
<?php endif; ?>

<?php
if ($additional_content) {
    echo wp_kses_post(wpautop(wptexturize($additional_content)));
}
?>

<?php
do_action('woocommerce_email_footer', $email); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook, not ours to prefix
