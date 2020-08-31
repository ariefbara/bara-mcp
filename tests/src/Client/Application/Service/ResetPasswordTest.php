<?php

namespace Client\Application\Service;

use Client\Dommain\Model\Client;
use Tests\TestBase;

class ResetPasswordTest extends TestBase
{

    protected $service;
    protected $clientRepository, $client;
    protected $dispatcher;
    protected $firmIdentifier = 'firm_identifier', $email = 'client@email.org',
            $resetPasswordCode = 'resetPasswordCode', $password = 'newPassword123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->client);
        
        $this->service = new ResetPassword($this->clientRepository);
    }
    protected function execute()
    {
        $this->service->execute($this->firmIdentifier, $this->email, $this->resetPasswordCode, $this->password);
    }
    public function test_execute_resetClientPassword()
    {
        $this->client->expects($this->once())
                ->method('resetPassword')
                ->with($this->resetPasswordCode, $this->password);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}
