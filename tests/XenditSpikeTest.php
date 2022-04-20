<?php

namespace Tests;

use Xendit\Invoice;
use Xendit\Xendit;

class XenditSpikeTest extends TestBase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    public function test_createInvoice()
    {
        Xendit::setApiKey('xnd_development_gkj6LgQXfYtG4Guez8YTqho88vHKWJzYkeddMhdUYPDZvX0tkeyxofWZHAUoXl');
        $params = [
            'external_id' => 'invoice_asdfasdfasdfasdf',
            'amount' => 100000,
            'description' => 'tagihan april',
            'invoice_duration' => 7*24*60*60,
            'customer' => [
                'given_name' => 'adi purnama',
                'email' => 'purnama.adi@gmail.com'
            ],
            'items' => [
                [
                    'name' => 'program name',
                    'quantity' => 1,
                    'price' => 100000
                ],
            ],
        ];
        $createdInvoice = Invoice::create($params);
var_dump($createdInvoice);
        $this->markAsSuccess();
    }
}
