<?php

declare(strict_types=1);

// phpcs:disable

/**
 * Plugin Name: WooCommerce Shipping Method Fallback
 * Description: Shipping method fallback for checkout in the case a specific shipping method is not loading.
 * Version: 1.0.0
 * Author: Oktagon
 * Author URI: https://www.oktagon.se/
 * Domain Path: includes/languages
 * Text Domain: wc-shipping-method-fallback
 * Namespace: Oktagon\WCShippingMethodFallback
 */

If (!defined('ABSPATH')) {
    exit;
}

// Just to include localization of plugin description
__(
    'Shipping method fallback for checkout in the case a specific shipping method is not loading.',
    'wc-shipping-method-fallback'
);

require_once(__DIR__ . '/autoload.php');

new \Oktagon\WCShippingMethodFallback\Meta();
new \Oktagon\WCShippingMethodFallback\ShippingMethodInit();
