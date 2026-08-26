<?php

namespace htrxuan\hdst;

if (!defined('ABSPATH')) {
    exit;
}

final class HDST_Core
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
        $this->includes();
        $this->init_hooks();
    }

    private function __clone()
    {
    }

    private function includes()
    {
        require_once HDST_PLUGIN_DIR . 'includes/class-hdst-carriers.php';
        require_once HDST_PLUGIN_DIR . 'includes/class-hdst-admin.php';
        require_once HDST_PLUGIN_DIR . 'includes/class-hdst-display.php';
    }

    private function init_hooks()
    {
        add_action('admin_notices', array($this, 'render_missing_woocommerce_notice'));

        if (!class_exists('WooCommerce')) {
            return;
        }

        add_filter('woocommerce_email_classes', array($this, 'register_email'));

        HDST_Admin::get_instance();
        HDST_Display::get_instance();
    }

    public function register_email($emails)
    {
        // WC_Emails::init() includes its own class-wc-email.php base class immediately
        // before applying this filter, so it's always safe to load our subclass here --
        // loading it any earlier (e.g. at plugins_loaded) would fatal on WC_Email not existing yet.
        require_once HDST_PLUGIN_DIR . 'includes/class-hdst-email.php';
        $emails['hdst_shipped'] = new HDST_Email();
        return $emails;
    }

    public function render_missing_woocommerce_notice()
    {
        $screen = get_current_screen();
        if (!$screen || 'plugins' !== $screen->id) {
            return;
        }

        if (!get_transient('hdst_wc_missing_notice')) {
            return;
        }
        delete_transient('hdst_wc_missing_notice');
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php esc_html_e('HDWebmobile Shipment Tracking requires WooCommerce to be installed and active. The plugin has been deactivated.', 'hdwebmobile-shipment-tracking'); ?>
            </p>
        </div>
        <?php
    }
}
