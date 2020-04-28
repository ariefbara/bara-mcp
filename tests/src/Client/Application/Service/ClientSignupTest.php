<?php

namespace Client\Application\Service;

use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ClientSignupTest extends TestBase
{

    protected $service;
    protected $clientRepository;
    protected $dispatcher;
    protected $name = 'new client', $email = 'new_client@email.org', $password = 'newPwd123';

    function setUp(): void
    {
        parent::setUp();

        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ClientSignup($this->clientRepository, $this->dispatcher);
    }

    private function execute()
    {
        return $this->service->execute($this->name, $this->email, $this->password);
    }

    function test_execute_addUserToRepository()
    {
        $this->clientRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }

    function test_execute_emailAlreadyRegistered_throwEx()
    {
        $this->clientRepository->expects($this->once())
            ->method('containRecordWithEmail')
            ->with($this->email)
            ->willReturn(true);
        $operation = function () {
            $this->execute();
        };
        $errorDetail = "conflict: email already registered";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }

    function test_execute_dispatchTalentToEventDispatcher()
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch');
        $this->execute();
    }

}
