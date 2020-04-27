<?php
return [
    [
        'key' => 'sales.paymentmethods.wirecard',
        'name' => 'Wirecard',
        'sort' => 120,
        'fields' => [
            [
                'name' => 'title',
                'title' => 'Título',
                'type' => 'text',
                'validation' => 'required',
                'locale_based' => true
            ], [
                'name' => 'description',
                'title' => 'Descrição',
                'type' => 'textarea',
                'locale_based' => true
            ], [
                'name' => 'token',
                'title' => 'Token',
                'type' => 'text',
                'validation' => 'required',
                'info' => ''
            ], [
                'name' => 'key',
                'title' => 'Key',
                'type' => 'text',
                'validation' => 'required',
                'info' => ''
            ], [
                'name' => 'public_key',
                'title' => 'Public Key',
                'type' => 'textarea',
                'validation' => 'required',
                'info' => ''
            ], [
                'name' => 'active',
                'title' => 'admin::app.admin.system.status',
                'type' => 'boolean',
                'validation' => 'required'
            ], [
                'name' => 'sandbox',
                'title' => 'Utilizar ambiente sandbox?',
                'type' => 'boolean',
                'validation' => 'required'
            ]
        ]
    ]
];