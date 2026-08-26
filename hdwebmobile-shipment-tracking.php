<?php

/**
 * Plugin Name: HDWebmobile Shipment Tracking
 * Plugin URI: https://hdwebmobile.com/plugins/hdwebmobile-shipment-tracking/
 * Description: Add a carrier and tracking number to any order and show it to the customer -- on their order page, in a "Your order has shipped" email, with strict output escaping and no bulk-import surface.
 * Version: 1.0.0
 * Author: htrxuan - Han Tran
 * Author URI: https://hdwebmobile.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hdwebmobile-shipment-tracking
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 * Requires PHP: 7.4
 * Requires at least: 6.9
 */

namespace htrxuan\hdst;

if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('HDST_VERSION', '1.0.0');
define('HDST_PLUGIN_FILE', __FILE__);
define('HDST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HDST_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once HDST_PLUGIN_DIR . 'includes/class-hdst-activator.php';

register_activation_hook(HDST_PLUGIN_FILE, array(HDST_Activator::class, 'activate'));
add_action('before_woocommerce_init', array(HDST_Activator::class, 'declare_hpos_compatibility'));

add_action('plugins_loaded', function () {
    require_once HDST_PLUGIN_DIR . 'includes/class-hdst-core.php';
    HDST_Core::get_instance();
});

add_filter('plugin_action_links_' . plugin_basename(HDST_PLUGIN_FILE), function ($links) {
    $donate_link = '<a href="https://paypal.me/htrxuan/20" target="_blank" style="color:#d54e21;font-weight:bold;">' . __('Donate', 'hdwebmobile-shipment-tracking') . '</a>';
    array_unshift($links, $donate_link);
    return $links;
});
