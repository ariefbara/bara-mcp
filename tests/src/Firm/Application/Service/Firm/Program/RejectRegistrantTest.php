<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Registrant;
use Tests\TestBase;

class RejectRegistrantTest extends TestBase
{
    protected $service;
    protected $registrantRepository, $registrant;
    
    protected $firmId = 'firmId', $programId = 'programId', $registrantId = 'registrantId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->registrantRepository = $this->buildMockOfInterface(RegistrantRepository::class);
        $this->registrantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId, $this->registrantId)
                ->willReturn($this->registrant);
        
        $this->service = new RejectRegistrant($this->registrantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->programId, $this->registrantId);
    }
    public function test_execute_rejectRegistrant()
    {
        $this->registrant->expects($this->once())
                ->method('reject');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->registrantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
