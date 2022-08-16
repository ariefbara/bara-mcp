<?php

namespace Payment\Domain\Task\PaymentGatewayAccount;

use Payment\Domain\Model\PaymentGatewayAccount;
use Resources\Application\Event\AdvanceDispatcher;

class SettleInvoice implements PaymentGatewayAccountTask
{

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(AdvanceDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param PaymentGatewayAccount $paymentGatewayAccount
     * @param string $payload invoiceId
     * @return void
     */
    public function execute(PaymentGatewayAccount $paymentGatewayAccount, $payload): void
    {
        $paymentGatewayAccount->settleInvoice($payload);
        $this->dispatcher->dispatch($paymentGatewayAccount);
    }

}
