<?php

namespace htrxuan\hdst;

if (!defined('ABSPATH')) {
    exit;
}

class HDST_Admin
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
        add_action('woocommerce_admin_order_data_after_order_details', array($this, 'render_fieldset'));
        add_action('woocommerce_process_shop_order_meta', array($this, 'save_fieldset'), 10, 2);
        add_action('admin_post_hdst_resend_notification', array($this, 'handle_resend_notification'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        require_once HDST_PLUGIN_DIR . 'includes/class-hdst-hub.php';
        add_filter('hdwebmobile_hub_tabs', array($this, 'register_hub_tabs'));
    }

    public function enqueue_admin_assets()
    {
        $screen = get_current_screen();
        if (!$screen || wc_get_page_screen_id('shop_order') !== $screen->id) {
            return;
        }

        wp_enqueue_script('hdst-admin-order-js', HDST_PLUGIN_URL . 'assets/js/hdst-admin.js', array(), HDST_VERSION, true);
    }

    public function render_fieldset($order)
    {
        $carrier         = $order->get_meta('_hdst_carrier');
        $tracking_number = $order->get_meta('_hdst_tracking_number');
        $tracking_url    = $order->get_meta('_hdst_tracking_url');
        $shipped_date    = $order->get_meta('_hdst_shipped_date');
        ?>
        <div class="hdst-tracking-fieldset" style="clear:both;padding-top:1em;margin-top:1em;border-top:1px solid #eee;">
            <h3><?php esc_html_e('Shipment Tracking', 'hdwebmobile-shipment-tracking'); ?></h3>
            <?php wp_nonce_field('hdst_save_tracking', 'hdst_tracking_nonce'); ?>
            <p class="form-field">
                <label for="hdst_carrier"><?php esc_html_e('Carrier', 'hdwebmobile-shipment-tracking'); ?></label>
                <select id="hdst_carrier" name="hdst_carrier" class="wc-enhanced-select" style="width:100%;">
                    <option value=""><?php esc_html_e('— Select carrier —', 'hdwebmobile-shipment-tracking'); ?></option>
                    <?php foreach (HDST_Carriers::get_carriers() as $key => $data) : ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($carrier, $key); ?>><?php echo esc_html($data['label']); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p class="form-field">
                <label for="hdst_tracking_number"><?php esc_html_e('Tracking Number', 'hdwebmobile-shipment-tracking'); ?></label>
                <input type="text" id="hdst_tracking_number" name="hdst_tracking_number" value="<?php echo esc_attr($tracking_number); ?>" class="short" maxlength="64" />
            </p>
            <p class="form-field hdst-custom-url-field" style="<?php echo 'other' === $carrier ? '' : 'display:none;'; ?>">
                <label for="hdst_custom_url"><?php esc_html_e('Custom Tracking URL', 'hdwebmobile-shipment-tracking'); ?></label>
                <input type="url" id="hdst_custom_url" name="hdst_custom_url" value="<?php echo esc_url($tracking_url); ?>" class="short" placeholder="https://" />
            </p>
            <p class="form-field">
                <label for="hdst_shipped_date"><?php esc_html_e('Shipped Date', 'hdwebmobile-shipment-tracking'); ?></label>
                <input type="date" id="hdst_shipped_date" name="hdst_shipped_date" value="<?php echo esc_attr($shipped_date); ?>" />
            </p>
            <?php if ($tracking_number) : ?>
                <p class="form-field">
                    <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=hdst_resend_notification&order_id=' . $order->get_id()), 'hdst_resend_' . $order->get_id())); ?>">
                        <?php esc_html_e('Resend shipping notification email', 'hdwebmobile-shipment-tracking'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }

    public function save_fieldset($order_id, $order)
    {
        if (!isset($_POST['hdst_tracking_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hdst_tracking_nonce'])), 'hdst_save_tracking')) {
            return;
        }

        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $carrier = isset($_POST['hdst_carrier']) ? sanitize_text_field(wp_unslash($_POST['hdst_carrier'])) : '';
        if (!HDST_Carriers::is_valid_carrier($carrier)) {
            $carrier = '';
        }

        $tracking_number_raw = isset($_POST['hdst_tracking_number']) ? sanitize_text_field(wp_unslash($_POST['hdst_tracking_number'])) : '';
        // Allowlist: letters, digits, spaces, and hyphens only -- closes off the stored-XSS
        // class a competing plugin (CVE-2026-3876) left open by not sanitizing this field.
        $tracking_number = substr(preg_replace('/[^A-Za-z0-9\- ]/', '', $tracking_number_raw), 0, 64);

        $custom_url = isset($_POST['hdst_custom_url']) ? esc_url_raw(wp_unslash($_POST['hdst_custom_url'])) : '';

        $shipped_date_raw = isset($_POST['hdst_shipped_date']) ? sanitize_text_field(wp_unslash($_POST['hdst_shipped_date'])) : '';
        $shipped_date      = preg_match('/^\d{4}-\d{2}-\d{2}$/', $shipped_date_raw) ? $shipped_date_raw : '';

        $tracking_url = HDST_Carriers::build_tracking_url($carrier, $tracking_number, $custom_url);

        $previous_tracking_number = $order->get_meta('_hdst_tracking_number');

        $order->update_meta_data('_hdst_carrier', $carrier);
        $order->update_meta_data('_hdst_tracking_number', $tracking_number);
        $order->update_meta_data('_hdst_tracking_url', $tracking_url);
        $order->update_meta_data('_hdst_shipped_date', $shipped_date);
        $order->save();

        if ('' === $previous_tracking_number && '' !== $tracking_number) {
            $this->ensure_email_class_loaded();
            HDST_Email::send_for_order($order);
        }
    }

    /**
     * admin-post.php requests (the "Resend notification" action) run a lighter WordPress
     * bootstrap than a normal wp-admin page load and don't necessarily initialize
     * WooCommerce's email system first, so WC_Email may not exist yet when this class'
     * `extends \WC_Email` declaration would otherwise be parsed. Calling WC()->mailer()
     * forces WC_Emails::init() to run, which loads includes/emails/class-wc-email.php.
     */
    private function ensure_email_class_loaded()
    {
        WC()->mailer();
        require_once HDST_PLUGIN_DIR . 'includes/class-hdst-email.php';
    }

    public function handle_resend_notification()
    {
        $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;

        if (!$order_id || !check_admin_referer('hdst_resend_' . $order_id)) {
            wp_die(esc_html__('Invalid request.', 'hdwebmobile-shipment-tracking'));
        }

        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('You do not have permission to do this.', 'hdwebmobile-shipment-tracking'));
        }

        $order = wc_get_order($order_id);
        if ($order && $order->get_meta('_hdst_tracking_number')) {
            $this->ensure_email_class_loaded();
            HDST_Email::send_for_order($order);
        }

        wp_safe_redirect(wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=wc-orders'));
        exit;
    }

    public function register_hub_tabs($tabs)
    {
        $tabs['shipment-tracking'] = array(
            'label'  => __('Shipment Tracking', 'hdwebmobile-shipment-tracking'),
            'order'  => 110,
            'render' => array($this, 'render_plugins_page'),
        );
        return $tabs;
    }

    public function render_plugins_page()
    {
        ?>
        <p><?php esc_html_e('Add a carrier and tracking number directly on each order\'s edit screen -- no separate settings needed.', 'hdwebmobile-shipment-tracking'); ?></p>
        <?php
    }
}
