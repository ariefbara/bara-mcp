<?php

namespace User\Application\Service;

use Tests\TestBase;
use User\Domain\Model\User;

class ChangeUserProfileTest extends TestBase
{
    protected $service;
    protected $userRepository, $user, $userId = 'userId';
    protected $firstName = 'hadi', $lastName = 'pranoto';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->user = $this->buildMockOfClass(User::class);
        $this->userRepository->expects($this->any())
                ->method('ofId')
                ->with($this->userId)
                ->willReturn($this->user);
        
        $this->service = new ChangeUserProfile($this->userRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->firstName, $this->lastName);
    }
    public function test_execute_changeUserProfile()
    {
        $this->user->expects($this->once())
                ->method('changeProfile')
                ->with($this->firstName, $this->lastName);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
