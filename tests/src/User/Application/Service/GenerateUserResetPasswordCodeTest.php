<?php

namespace User\Application\Service;

use Resources\Application\Event\Dispatcher;
use Tests\TestBase;
use User\Domain\Model\User;

class GenerateUserResetPasswordCodeTest extends TestBase
{
    protected $service;
    protected $userRepository, $user, $userEmail = 'user@email.org';
    protected $dispatcher;


    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->user = $this->buildMockOfClass(User::class);
        $this->userRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->userEmail)
                ->willReturn($this->user);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new GenerateUserResetPasswordCode($this->userRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userEmail);
    }
    public function test_execute_resetUserPassword()
    {
        $this->user->expects($this->once())
                ->method('generateResetPasswordCode');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    public function test_execute_dispatchUserToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->user);
        $this->execute();
    }
}
