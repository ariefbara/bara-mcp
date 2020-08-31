<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\User;
use Resources\Application\Service\Mailer;
use Tests\TestBase;

class SendUserResetPasswordCodeMailTest extends TestBase
{
    protected $service;
    protected $userRepository, $user;
    protected $mailer;
    protected $userId = 'userId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->buildMockOfClass(User::class);
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->userRepository->expects($this->any())
                ->method('ofId')
                ->with($this->userId)
                ->willReturn($this->user);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
        
        $this->service = new SendUserResetPasswordCodeMail($this->userRepository, $this->mailer);
    }
    
    public function test_execute_sendUserResetPasswordCodeMail()
    {
        $this->user->expects($this->once())
                ->method('sendResetPasswordCodeMail')
                ->with($this->mailer);
        
        $this->service->execute($this->userId);
    }
}
