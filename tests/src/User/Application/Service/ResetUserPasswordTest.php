<?php

namespace User\Application\Service;

use Tests\TestBase;
use User\Domain\Model\User;

class ResetUserPasswordTest extends TestBase
{
    protected $service;
    protected $userRepository, $user, $userEmail = 'user@email.org';
    protected $resetPasswordCode = 'string represent reset password code';
    protected $newPassword = 'newPwd123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->user = $this->buildMockOfClass(User::class);
        $this->userRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->userEmail)
                ->willReturn($this->user);
        
        $this->service = new ResetUserPassword($this->userRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userEmail, $this->resetPasswordCode, $this->newPassword);
    }
    public function test_execute_resetUserPassword()
    {
        $this->user->expects($this->once())
                ->method('resetPassword')
                ->with($this->resetPasswordCode, $this->newPassword);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
