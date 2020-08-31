<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Client;
use Resources\Application\Service\Mailer;
use Tests\TestBase;

class SendClientResetPasswordCodeMailTest extends TestBase
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
        
        $this->service = new SendClientResetPasswordCodeMail($this->clientRepository, $this->mailer);
    }
    
    public function test_execute_sendResetPasswordCodeToClient()
    {
        $this->client->expects($this->once())
                ->method('sendResetPasswordCodeMail')
                ->with($this->mailer);
        $this->service->execute($this->firmId, $this->clientId);
    }
}
