<?php

namespace Payment\Application\Service\PaymentGatewayAccount;

use Doctrine\ORM\EntityManager;
use Payment\Domain\Task\PaymentGatewayAccount\PaymentGatewayAccountTask;

class ExecuteTask
{

    /**
     * 
     * @var PaymentGatewayAccountRepository
     */
    protected $paymentGatewayAccoutRepository;

    /**
     * 
     * @var EntityManager
     */
    protected $em;

    public function __construct(PaymentGatewayAccountRepository $paymentGatewayAccountRepository, EntityManager $em)
    {
        $this->paymentGatewayAccoutRepository = $paymentGatewayAccountRepository;
        $this->em = $em;
    }

    public function execute(string $token, PaymentGatewayAccountTask $task, $payload): void
    {
        $this->paymentGatewayAccoutRepository->ofToken($token)
                ->executePaymentGatewayAccountTask($task, $payload);
        $this->em->flush();
    }

}
