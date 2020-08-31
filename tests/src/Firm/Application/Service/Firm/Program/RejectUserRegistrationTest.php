<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\UserRegistrant;
use Tests\TestBase;

class RejectUserRegistrationTest extends TestBase
{
    protected $service;
    protected $userRegistrantRepository, $userRegistrant;
    
    protected $firmId = 'firmId', $programId = 'programId', $userRegistrantId = 'userRegistrantId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRegistrant = $this->buildMockOfClass(UserRegistrant::class);
        $this->userRegistrantRepository = $this->buildMockOfInterface(UserRegistrantRepository::class);
        $this->userRegistrantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId, $this->userRegistrantId)
                ->willReturn($this->userRegistrant);
        
        $this->service = new RejectUserRegistration($this->userRegistrantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->programId, $this->userRegistrantId);
    }
    public function test_execute_rejectUserRegistrant()
    {
        $this->userRegistrant->expects($this->once())
                ->method('reject');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userRegistrantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
