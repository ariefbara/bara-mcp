<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Tests\TestBase;

class ClientResetPasswordTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client, $clientEmail = 'client@email.org';
    protected $resetPasswordCode = 'string represent reset password code';
    protected $newPassword = 'newPwd123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->clientEmail)
                ->willReturn($this->client);
        
        $this->service = new ClientResetPassword($this->clientRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->clientEmail, $this->resetPasswordCode, $this->newPassword);
    }
    public function test_execute_resetClientPassword()
    {
        $this->client->expects($this->once())
                ->method('resetPassword')
                ->with($this->resetPasswordCode, $this->newPassword);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
