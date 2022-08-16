<?php

namespace Payment\Application\Service\PaymentGatewayAccount;

use Doctrine\ORM\EntityManager;
use Payment\Domain\Model\PaymentGatewayAccount;
use Payment\Domain\Task\PaymentGatewayAccount\PaymentGatewayAccountTask;
use Tests\TestBase;

class ExecuteTaskTest extends TestBase
{

    protected $paymentGatewayAccountRepository;
    protected $paymentGatewayAccount;
    protected $token = 'paymentGatewayToken';
    protected $em;
    
    protected $service;
    protected $task, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGatewayAccountRepository = $this->buildMockOfInterface(PaymentGatewayAccountRepository::class);
        $this->paymentGatewayAccount = $this->buildMockOfClass(PaymentGatewayAccount::class);
        
        $this->em = $this->buildMockOfClass(EntityManager::class);

        $this->service = new ExecuteTask($this->paymentGatewayAccountRepository, $this->em);

        $this->task = $this->buildMockOfInterface(PaymentGatewayAccountTask::class);
    }

    protected function execute()
    {
        $this->paymentGatewayAccountRepository->expects($this->any())
                ->method('ofToken')
                ->with($this->token)
                ->willReturn($this->paymentGatewayAccount);
        $this->service->execute($this->token, $this->task, $this->payload);
    }
    public function test_execute_xenditAccountExecuteTask()
    {
        $this->paymentGatewayAccount->expects($this->once())
                ->method('executePaymentGatewayAccountTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_flushEntityManager()
    {
        $this->em->expects($this->once())
                ->method('flush');
        $this->execute();
    }

}
