<?php

declare(strict_types=1);

namespace Oktagon\WCShippingMethodFallback;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class ShippingMethod extends \WC_Shipping_Method
{
    const METHOD_ID = 'wc-shipping-method-fallback';
    const METHOD_TITLE = 'Shipping Method Fallback';

    public $cost = 0.0;

    public $description = '';

    public $enabled = 'yes';

    public $id = self::METHOD_ID;

    public $instance_id = 0;

    public $instance_form_fields = [];

    public $method_title = self::METHOD_TITLE;

    public $method_description = 'Shipping method fallback for checkout in the case a specific shipping method is not loading.';

    public $supports = [
        'instance-settings',
        'shipping-zones',
    ];

    public $tax_status = 'taxable';

    public $title;

    private $foundShippingOptions;

    private $apiConnectionError;

    public function __construct(
        int $instanceId = 0,
        array $settings = null
    ) {
        $this->instance_id = absint($instanceId);
        $this->instance_form_fields = [
            'title' => [
                'title' => __('Method title', 'woocommerce'),
                'type' => 'text',
                'description' => __(
                    'Only visible when a specific shipping method is not loading.',
                    'wc-shipping-method-fallback'
                ),
                'default' => self::METHOD_TITLE,
                'desc_tip' => false,
            ],
            'cost' => [
                'description' => __(
                    'Only visible when a specific shipping method is not loading.',
                    'wc-shipping-method-fallback'
                ),
                'desc_tip' => false,
                'title' => __('Cost', 'woocommerce'),
                'type' => 'text',
            ],
            'shipping_method' => [
                'class' => 'wc-enhanced-select',
                'desc_tip' => false,
                'description' => __(
                    'This shipping method will only be displayed if the selected shipping method cannot be found.',
                    'wc-shipping-method-fallback'
                ),
                'options' => $this->getShippingMethods(),
                'title' => __(
                    'Shipping method to search for',
                    'wc-shipping-method-fallback'
                ),
                'type' => 'select',
            ],
            'tax_status' => [
                'title' => __('Tax status', 'woocommerce'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => 'taxable',
                'options' => [
                    'taxable' => __('Taxable', 'woocommerce'),
                    'none' => _x('None', 'Tax status', 'woocommerce'),
                ],
            ]
        ];
        parent::init_form_fields();
        parent::init_settings();
        parent::init_instance_settings();
        $this->cost = $this->instance_settings['cost'];
        $this->title = $this->instance_settings['title'];
        $this->tax_status = $this->instance_settings['tax_status'];
        if (
            !empty($settings)
            && is_array($settings)
        ) {
            $this->enabled = $settings['enabled'];
        }
    }

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    /**
     * @param array $package
     * @return bool
     * @since Woocommerce 3.0.0
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function is_available($package)
    {
        $available = false;
        if ($this->enabled === 'yes') {
            $available = true;
        }

        // Allow third-party customization here
        $available = (bool) apply_filters(
            'wc-shipping-method-fallback-shipping-available',
            $available,
            $this,
            $package
        );

        $available = (bool) apply_filters(
            'woocommerce_shipping_' . $this->id . '_is_available',
            $available,
            $package
        );

        return $available;
    }
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    /**
     * Based on WooCommerce Flat Rate shipping method.
     * @param array $package
     * @return void
     * @see \WC_Shipping_Flat_Rate->calculate_shipping()
     * @since WooCommerce 3.0.0, Wordpress 3.1.0
     * @see $package defined in \WC_Cart->get_shipping_packages()
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function calculate_shipping(
        $package = []
    ) {
        $foundShippingMethod = false;
        if (
            $this->instance_settings['shipping_method']
            && !empty($package['rates'])
            && is_array($package['rates'])
        ) {
            foreach (array_keys($package['rates']) as $rateId) {
                if (strpos($rateId, $this->instance_settings['shipping_method']) === 0) {
                    $foundShippingMethod = true;
                    break;
                }
            }
        }
        if ($foundShippingMethod) {
            return;
        }

        $rate = [
            'cost' => $this->cost,
            'id' => $this->get_rate_id(),
            'label' => $this->title,
            'package' => $package,
        ];

        // Determine if we have a free-shipping coupon
        $hasFreeShippingCoupon = false;
        if ($coupons = \WC()->cart->get_coupons()) {
            foreach ($coupons as $coupon) {
                if (
                    $coupon->is_valid()
                    && $coupon->get_free_shipping()
                ) {
                    $hasFreeShippingCoupon = true;
                    break;
                }
            }
        }

        // Support free shipping coupons
        if (
            $hasFreeShippingCoupon
        ) {
            $rate['cost'] = 0;
            $rate['taxes'] = false;
        }

        \do_action(
            'wc-shipping-method-fallback-add-rate',
            $this,
            $rate
        );
        \do_action(
            sprintf(
                'woocommerce_%s_shipping_add_rate',
                $this->id
            ),
            $this,
            $rate
        );
        $this->add_rate($rate);
        return;
    }
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    private function getShippingMethods(): array
    {
        global $wpdb;
        $methods = [];
        $rawMethods = $wpdb->get_results(
            "SELECT DISTINCT method_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods ORDER BY method_id ASC",
            'ARRAY_A'
        );
        if ($rawMethods) {
            $methods = [];
            foreach ($rawMethods as $rawMethod) {
                if (
                    !empty($rawMethod['method_id'])
                    && $rawMethod['method_id'] !== self::METHOD_ID
                ) {
                    $methods[] = (string) $rawMethod['method_id'];
                }
            }
        }
        return $methods;
    }
}
