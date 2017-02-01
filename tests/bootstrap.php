<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Mage.php';

spl_autoload_unregister(array(\Varien_Autoload::instance(), 'autoload'));

spl_autoload_register(function ($className) {
    $filePath = strtr(
        ltrim($className, '\\'),
        array(
            '\\' => '/',
            '_'  => '/'
        )
    );
    @include $filePath . '.php';
});
