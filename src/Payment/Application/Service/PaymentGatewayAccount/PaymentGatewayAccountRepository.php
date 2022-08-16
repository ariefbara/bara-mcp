<?php

namespace Payment\Application\Service\PaymentGatewayAccount;

use Payment\Domain\Model\PaymentGatewayAccount;

interface PaymentGatewayAccountRepository
{

    public function ofToken(string $token): PaymentGatewayAccount;
}
