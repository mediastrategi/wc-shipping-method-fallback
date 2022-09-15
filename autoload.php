<?php

declare(strict_types=1);

spl_autoload_register(
    function (string $class) {
        if (
            strpos(
                $class,
                'Oktagon\\WCShippingMethodFallback\\'
            ) !== false
        ) {
            $class = str_replace(
                'Oktagon\\WCShippingMethodFallback\\',
                '',
                $class
            );
            $classFilename = $class;
            $newClassFilename = '';
            for ($i = 0; $i < strlen($classFilename); $i++) {
                $char = $classFilename[$i];
                $prepend = '';
                if (
                    $i > 0
                    && $char === strtoupper($char)
                ) {
                    $prepend = '-';
                }
                $newClassFilename .=
                    $prepend . strtolower($char);
            }
            $filename = sprintf(
                '%s/includes/%s.php',
                __DIR__,
                str_replace(
                    '\\',
                    '/',
                    $newClassFilename
                )
            );
            if (
                file_exists($filename)
                && is_file($filename)
            ) {
                require_once($filename);
            } else {
                throw new \Exception(
                    sprintf(
                        __(
                            'WooCommerce Shipping Method Fallback for WooCommerce autoloader failed for class "%s" at "%s"!',
                            'wc-shipping-method-fallback'
                        ),
                        $class,
                        $filename
                    )
                );
            }
        }
    }
);
