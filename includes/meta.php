<?php

declare(strict_types=1);

namespace Oktagon\WCShippingMethodFallback;

class Meta
{
    public function __construct()
    {
        \load_plugin_textdomain(
            'wc-shipping-method-fallback',
            false,
            basename(dirname(__DIR__)) . '/includes/languages'
        );
    }
}
