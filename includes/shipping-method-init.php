<?php

declare(strict_types=1);

namespace Oktagon\WCShippingMethodFallback;

class ShippingMethodInit
{
    public function __construct()
    {
        \add_filter(
            'woocommerce_shipping_methods',
            [
                $this,
                'add'
            ]
        );
    }

    public function add(array $methods): array
    {
        $methods[ShippingMethod::METHOD_ID] = ShippingMethod::class;
        return $methods;
    }
}
