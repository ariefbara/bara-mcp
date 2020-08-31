<?php

namespace User\Application\Service;

use Tests\TestBase;
use User\Domain\Model\User;

class ChangeUserPasswordTest extends TestBase
{
    protected $service;
    protected $userRepository, $user, $userId = 'userId';
    protected $previousPassword = "oldPwd123", $newPassword = 'newPwd123';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->user = $this->buildMockOfClass(User::class);
        $this->userRepository->expects($this->any())
                ->method('ofId')
                ->with($this->userId)
                ->willReturn($this->user);
        
        $this->service = new ChangeUserPassword($this->userRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->previousPassword, $this->newPassword);
    }
    
    public function test_execute_changeUserPassword()
    {
        $this->user->expects($this->once())
                ->method('changePassword')
                ->with($this->previousPassword, $this->newPassword);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
