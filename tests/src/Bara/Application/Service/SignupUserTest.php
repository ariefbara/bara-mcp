<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\UserData;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class SignupUserTest extends TestBase
{
    protected $service;
    protected $userRepository, $nextIdentity = 'nextId';
    protected $dispatcher;
    protected $userData, $email = 'covid19@hadipranoto.com';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->userRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextIdentity);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new SignupUser($this->userRepository, $this->dispatcher);
        
        $this->userData = new UserData('hadi', 'pranoto', $this->email, 'obatcovid19');
    }
    
    protected function execute()
    {
        return $this->service->execute($this->userData);
    }
    
    public function test_execute_addUserToRepository()
    {
        $this->userRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_dispatcheDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch');
        $this->execute();
    }
}
