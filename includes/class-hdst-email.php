<?php

namespace htrxuan\hdst;

if (!defined('ABSPATH')) {
    exit;
}

class HDST_Email extends \WC_Email
{

    public function __construct()
    {
        $this->id             = 'hdst_shipped';
        $this->customer_email = true;
        $this->title          = __('Order Shipped', 'hdwebmobile-shipment-tracking');
        $this->description    = __('Sent to a customer when tracking information is added to their order.', 'hdwebmobile-shipment-tracking');
        $this->template_html  = 'hdst-shipped.php';
        $this->template_plain = 'plain/hdst-shipped.php';
        $this->template_base  = HDST_PLUGIN_DIR . 'emails/';
        $this->placeholders   = array(
            '{order_number}'    => '',
            '{carrier}'         => '',
            '{tracking_number}' => '',
            '{tracking_url}'    => '',
        );

        parent::__construct();
    }

    public function get_default_subject()
    {
        return __('Your order #{order_number} has shipped', 'hdwebmobile-shipment-tracking');
    }

    public function get_default_heading()
    {
        return __('Your order is on its way!', 'hdwebmobile-shipment-tracking');
    }

    /**
     * Triggers the shipped-notification email for an order. Static entry point so it can
     * be called both from the auto-trigger (empty -> non-empty transition) and the
     * "Resend notification" admin action without instantiating this class twice.
     *
     * @param \WC_Order $order
     * @return bool True if the email was sent.
     */
    public static function send_for_order($order)
    {
        $emails = WC()->mailer()->get_emails();
        $email  = isset($emails['hdst_shipped']) ? $emails['hdst_shipped'] : new self();
        return $email->trigger($order);
    }

    public function trigger($order)
    {
        $this->setup_locale();

        $this->object = $order;
        $this->recipient = $order->get_billing_email();

        $this->placeholders['{order_number}']    = $order->get_order_number();
        $this->placeholders['{carrier}']         = HDST_Carriers::get_label($order->get_meta('_hdst_carrier'));
        $this->placeholders['{tracking_number}'] = $order->get_meta('_hdst_tracking_number');
        $this->placeholders['{tracking_url}']    = $order->get_meta('_hdst_tracking_url');

        if (!$this->is_enabled() || !$this->get_recipient()) {
            $this->restore_locale();
            return false;
        }

        $sent = $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());

        $this->restore_locale();

        return $sent;
    }

    public function get_content_html()
    {
        return wc_get_template_html(
            $this->template_html,
            array(
                'order'              => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'carrier'            => $this->placeholders['{carrier}'],
                'tracking_number'    => $this->placeholders['{tracking_number}'],
                'tracking_url'       => $this->placeholders['{tracking_url}'],
                'sent_to_admin'      => false,
                'plain_text'         => false,
                'email'              => $this,
            ),
            '',
            $this->template_base
        );
    }

    public function get_content_plain()
    {
        return wc_get_template_html(
            $this->template_plain,
            array(
                'order'              => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'carrier'            => $this->placeholders['{carrier}'],
                'tracking_number'    => $this->placeholders['{tracking_number}'],
                'tracking_url'       => $this->placeholders['{tracking_url}'],
                'sent_to_admin'      => false,
                'plain_text'         => true,
                'email'              => $this,
            ),
            '',
            $this->template_base
        );
    }

    public function get_default_additional_content()
    {
        return __('If you have any questions, just reply to this email — we\'re happy to help.', 'hdwebmobile-shipment-tracking');
    }
}
