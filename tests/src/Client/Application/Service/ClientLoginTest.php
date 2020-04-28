<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Resources\Exception\RegularException;
use Tests\TestBase;

class ClientLoginTest extends TestBase
{

    protected $service;
    protected $clientRepository, $client, $email = 'client@email.org', $password = 'password123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->client = $this->buildMockOfClass(Client::class);

        $this->service = new ClientLogin($this->clientRepository);
    }

    protected function execute()
    {
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->email)
                ->willReturn($this->client);
        $this->client->expects($this->any())
                ->method('passwordMatch')
                ->with($this->password)
                ->willReturn(true);
        $this->client->expects($this->any())
                ->method('isActivated')
                ->willReturn(true);
        return $this->service->execute($this->email, $this->password);
    }

    public function test_execute_returnClientEntity()
    {
        $this->assertEquals($this->client, $this->execute());
    }

    public function test_execute_clientNotFound_throwEx()
    {
        $this->clientRepository->expects($this->once())
                ->method('ofEmail')
                ->with($this->email)
                ->willThrowException(RegularException::notFound('not found: client not found'));
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, "Unauthorized", $errorDetail);
    }

    public function test_execute_passwordNotMatch_throwEx()
    {
        $this->client->expects($this->once())
                ->method('passwordMatch')
                ->with($this->password)
                ->willReturn(false);
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, "Unauthorized", $errorDetail);
    }

    public function test_execute_inactiveClient_throwEx()
    {
        $this->client->expects($this->once())
                ->method('isActivated')
                ->willReturn(false);
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'unauthorized: account not activated';
        $this->assertRegularExceptionThrowed($operation, "Unauthorized", $errorDetail);
    }

}
