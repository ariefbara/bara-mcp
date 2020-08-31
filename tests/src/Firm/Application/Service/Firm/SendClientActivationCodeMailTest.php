<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Client;
use Resources\Application\Service\Mailer;
use Tests\TestBase;

class SendClientActivationCodeMailTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client;
    protected $mailer;
    
    protected $firmId = 'firmId', $clientId = 'clientId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
        
        $this->service = new SendClientActivationCodeMail($this->clientRepository, $this->mailer);
        
    }
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId);
    }
    public function test_execute_executeClientsSendActivationCodeMailMethod_expectedResult()
    {
        $this->client->expects($this->once())
                ->method('sendActivationCodeMail')
                ->with($this->mailer);
        $this->execute();
    }
}
