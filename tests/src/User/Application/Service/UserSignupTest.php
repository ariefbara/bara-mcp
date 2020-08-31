<?php

namespace User\Application\Service;

use Resources\Application\Event\Dispatcher;
use Tests\TestBase;
use User\Domain\Model\UserData;

class UserSignupTest extends TestBase
{

    protected $service;
    protected $userRepository;
    protected $dispatcher;
    protected $userData, $email = 'user@email.com';

    function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new UserSignup($this->userRepository, $this->dispatcher);
        $this->userData = new UserData('hadi', 'pranoto', $this->email, 'password123');
    }

    private function execute()
    {
        return $this->service->execute($this->userData);
    }

    function test_execute_addUserToRepository()
    {
        $this->userRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }

    function test_execute_emailAlreadyRegistered_throwEx()
    {
        $this->userRepository->expects($this->once())
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
