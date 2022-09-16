<?php

declare(strict_types=1);

namespace Oktagon\WCShippingMethodFallback;

class Meta
{
    public function __construct()
    {
        \add_action(
            'admin_enqueue_scripts',
            [
                $this,
                'adminEnqueueScripts'
           ]
        );
        \add_action(
            'init',
            array(
                $this,
                'init'
            )
        );
        \add_action(
            'wp_ajax_wc_shipping_method_fallback_shipping_methods',
            array(
                $this,
                'ajaxGetShippingMethods'
            )
        );
    }

    public function ajaxGetShippingMethods(): void
    {
        echo json_encode($this->getShippingMethods());
        wp_die();
    }

    public function init(): void
    {
        \load_plugin_textdomain(
            'wc-shipping-method-fallback',
            false,
            basename(dirname(__DIR__)) . '/includes/languages'
        );
    }

    public function adminEnqueueScripts(): void
    {
        \wp_register_script(
            'wc-shipping-method-fallback-admin-script',
            \plugins_url(
                'includes/assets/admin.js',
                dirname(__FILE__)
            ),
            array('jquery'),
            '220916'
        );
        \wp_enqueue_script(
            'wc-shipping-method-fallback-admin-script'
        );
        \wp_localize_script(
            'wc-shipping-method-fallback-admin-script',
            'WC_Shipping_Method_Fallback',
            [
                'AjaxUrl' => \admin_url('admin-ajax.php'),
            ]
        );
        \wp_enqueue_style(
            'wc-shipping-method-fallback-admin-style',
            \plugins_url(
                'includes/assets/admin.css',
                dirname(__FILE__)
            ),
            array(),
            '220916'
        );
    }

    private function getShippingMethods(): array
    {
        global $wpdb;
        $methods = [];
        $rawMethods = $wpdb->get_results(
            "SELECT DISTINCT method_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods ORDER BY method_id ASC",
            'ARRAY_A'
        );
        if (
            is_array($rawMethods)
            && !empty($rawMethods)
        ) {
            $methods = [];
            foreach ($rawMethods as $rawMethod) {
                if (
                    is_array($rawMethod)
                    && !empty($rawMethod['method_id'])
                    && $rawMethod['method_id'] !==
                        \Oktagon\WCShippingMethodFallback\ShippingMethod::METHOD_ID
                ) {
                    $methods[] = (string) $rawMethod['method_id'];
                }
            }
        }
        return $methods;
    }
}
