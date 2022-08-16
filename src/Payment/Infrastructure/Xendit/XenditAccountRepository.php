<?php

namespace Payment\Infrastructure\Xendit;

use Payment\Application\Service\PaymentGatewayAccount\PaymentGatewayAccountRepository;
use Payment\Domain\Model\PaymentGatewayAccount;
use Resources\Exception\RegularException;
use function env;

class XenditAccountRepository implements PaymentGatewayAccountRepository
{

    public function ofToken(string $token): PaymentGatewayAccount
    {
        if ($token !== env('XENDIT_CALLBACK_TOKEN')) {
            throw RegularException::forbidden('only xendit can make this request');
        }
        return new PaymentGatewayAccount();
    }

}
