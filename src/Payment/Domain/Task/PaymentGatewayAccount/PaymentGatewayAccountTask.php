<?php

namespace Payment\Domain\Task\PaymentGatewayAccount;

use Payment\Domain\Model\PaymentGatewayAccount;

interface PaymentGatewayAccountTask
{

    public function execute(PaymentGatewayAccount $paymentGatewayAccount, $payload): void;
}
