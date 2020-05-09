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
                'name' => 'store_name',
                'title' => 'Store name',
                'type' => 'text',
                'validation' => 'required',
                'info' => ''
            ], [
                'name' => 'webhook_url',
                'title' => 'Webhook URL',
                'type' => 'text',
                'validation' => '',
                'info' => 'A URL é utilizada apenas no momento de criar o webhook. Criar webhook: /wirecard/createwebhook; Listar webhooks: /wirecard/listwebhook; Remover webhook: /wirecard/deletewebhook/{notification_id}'
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