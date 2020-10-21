<?php

namespace Notification\Application\Service\User;

use Notification\Domain\Model\User;
use Tests\TestBase;

class CreateActivationMailTest extends TestBase
{
    protected $userMailRepository, $nextId = "nextId";
    protected $userRepository, $user;
    protected $service;
    protected $userId = "userId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userMailRepository = $this->buildMockOfInterface(UserMailRepository::class);
        $this->userMailRepository->expects($this->any())
                ->method("nextIdentity")->willReturn($this->nextId);
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->userRepository->expects($this->any())
                ->method("ofId")
                ->with($this->userId)
                ->willReturn($this->user);
        
        $this->service = new CreateActivationMail($this->userMailRepository, $this->userRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId);
    }
    public function test_execute_addUserMailToRepository()
    {
        $this->user->expects($this->once())
                ->method("createActivationMail")
                ->with($this->nextId);
        $this->userMailRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
}
