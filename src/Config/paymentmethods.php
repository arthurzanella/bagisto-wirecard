<?php
return [
    'wirecard'  => [
        'code'              => 'wirecard',
        'title'             => 'Wirecard',
        'store_name'        => 'Wirecard',
        'description'       => 'Pague sua compra com Wirecard',
        'class'             => \ArthurZanella\Wirecard\Payment\Wirecard::class,
        'active'            => true,
        'sandbox'            => true,
        'sort'              => 100,
        'channel_based'     => false,
    ],
];