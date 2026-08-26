<?php

namespace htrxuan\hdst;

if (!defined('ABSPATH')) {
    exit;
}

class HDST_Carriers
{

    /**
     * Carrier key => label + tracking URL template. {tracking_number} is replaced with
     * the sanitized tracking number. 'other' is handled separately (admin-supplied URL).
     */
    public static function get_carriers()
    {
        return array(
            'usps'   => array(
                'label'        => __('USPS', 'hdwebmobile-shipment-tracking'),
                'url_template' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels={tracking_number}',
            ),
            'ups'    => array(
                'label'        => __('UPS', 'hdwebmobile-shipment-tracking'),
                'url_template' => 'https://www.ups.com/track?tracknum={tracking_number}',
            ),
            'fedex'  => array(
                'label'        => __('FedEx', 'hdwebmobile-shipment-tracking'),
                'url_template' => 'https://www.fedex.com/fedextrack/?trknbr={tracking_number}',
            ),
            'dhl'    => array(
                'label'        => __('DHL', 'hdwebmobile-shipment-tracking'),
                'url_template' => 'https://www.dhl.com/en/express/tracking.html?AWB={tracking_number}',
            ),
            'ghn'    => array(
                'label'        => __('GHN (Giao Hang Nhanh)', 'hdwebmobile-shipment-tracking'),
                'url_template' => 'https://tracking.ghn.dev/?order_code={tracking_number}',
            ),
            'ghtk'   => array(
                'label'        => __('GHTK (Giao Hang Tiet Kiem)', 'hdwebmobile-shipment-tracking'),
                'url_template' => 'https://i.ghtk.vn/{tracking_number}',
            ),
            'vnpost' => array(
                'label'        => __('Vietnam Post', 'hdwebmobile-shipment-tracking'),
                'url_template' => 'https://tracuu.vnpost.vn/?id={tracking_number}',
            ),
            'other'  => array(
                'label'        => __('Other (custom link)', 'hdwebmobile-shipment-tracking'),
                'url_template' => '',
            ),
        );
    }

    public static function is_valid_carrier($carrier)
    {
        return array_key_exists($carrier, self::get_carriers());
    }

    public static function get_label($carrier)
    {
        $carriers = self::get_carriers();
        return isset($carriers[$carrier]) ? $carriers[$carrier]['label'] : '';
    }

    /**
     * Builds the tracking URL for a known carrier, or returns the admin-supplied custom
     * URL (already esc_url_raw()'d at save time) when the carrier is 'other'.
     */
    public static function build_tracking_url($carrier, $tracking_number, $custom_url = '')
    {
        if ('other' === $carrier) {
            return $custom_url;
        }

        $carriers = self::get_carriers();
        if (!isset($carriers[$carrier]) || empty($carriers[$carrier]['url_template'])) {
            return '';
        }

        return str_replace('{tracking_number}', rawurlencode($tracking_number), $carriers[$carrier]['url_template']);
    }
}
