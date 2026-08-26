<?php

namespace htrxuan\hdst;

if (!defined('ABSPATH')) {
    exit;
}

class HDST_Display
{

    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('woocommerce_order_details_after_order_table', array($this, 'render_customer_box'));
        add_action('woocommerce_email_after_order_table', array($this, 'render_email_box'), 10, 2);
    }

    public function render_customer_box($order)
    {
        $this->render_box($order);
    }

    public function render_email_box($order, $sent_to_admin)
    {
        if ($sent_to_admin) {
            return;
        }
        $this->render_box($order);
    }

    private function render_box($order)
    {
        $tracking_number = $order->get_meta('_hdst_tracking_number');
        if (!$tracking_number) {
            return;
        }

        $carrier      = $order->get_meta('_hdst_carrier');
        $tracking_url = $order->get_meta('_hdst_tracking_url');
        $shipped_date = $order->get_meta('_hdst_shipped_date');
        $carrier_label = HDST_Carriers::get_label($carrier);
        ?>
        <div class="hdst-tracking-box" style="margin:1.5em 0;padding:1em;border:1px solid #dcdcde;border-radius:4px;">
            <h2><?php esc_html_e('Shipping', 'hdwebmobile-shipment-tracking'); ?></h2>
            <?php if ($carrier_label) : ?>
                <p><strong><?php esc_html_e('Carrier:', 'hdwebmobile-shipment-tracking'); ?></strong> <?php echo esc_html($carrier_label); ?></p>
            <?php endif; ?>
            <p><strong><?php esc_html_e('Tracking Number:', 'hdwebmobile-shipment-tracking'); ?></strong> <?php echo esc_html($tracking_number); ?></p>
            <?php if ($shipped_date) : ?>
                <p><strong><?php esc_html_e('Shipped:', 'hdwebmobile-shipment-tracking'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($shipped_date))); ?></p>
            <?php endif; ?>
            <?php if ($tracking_url) : ?>
                <p><a href="<?php echo esc_url($tracking_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Track your package', 'hdwebmobile-shipment-tracking'); ?></a></p>
            <?php endif; ?>
        </div>
        <?php
    }
}
