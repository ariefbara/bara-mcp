<?php

namespace User\Application\Service;

use Tests\TestBase;
use User\Domain\Model\User;

class ActivateUserTest extends TestBase
{
    protected $service;
    protected $userRepository, $user, $userEmail = 'client@email.org';
    protected $activationCode = 'string represent activation code';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->user = $this->buildMockOfClass(User::class);
        $this->userRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->userEmail)
                ->willReturn($this->user);
        
        $this->service = new ActivateUser($this->userRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userEmail, $this->activationCode);
    }
    
    public function test_execute_activateClient()
    {
        $this->user->expects($this->once())
                ->method('activate')
                ->with($this->activationCode);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
