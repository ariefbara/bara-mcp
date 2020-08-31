<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\User;
use Resources\Application\Service\Mailer;
use Tests\TestBase;

class SendUserActivationCodeMailTest extends TestBase
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
        
        $this->service = new SendUserActivationCodeMail($this->userRepository, $this->mailer);
    }
    
    public function test_execute_sendUserActivationCodeMail()
    {
        $this->user->expects($this->once())
                ->method('sendActivationCodeMail')
                ->with($this->mailer);
        
        $this->service->execute($this->userId);
    }
    
}
