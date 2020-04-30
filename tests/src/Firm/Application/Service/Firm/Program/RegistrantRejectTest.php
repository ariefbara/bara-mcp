<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Registrant;
use Tests\TestBase;

class RegistrantRejectTest extends TestBase
{
    protected $service;
    protected $programCompositionId;
    protected $registrantRepository, $registrant, $registrantId = 'registrant-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programCompositionId = $this->buildMockOfClass(ProgramCompositionId::class);
        
        $this->registrantRepository = $this->buildMockOfInterface(RegistrantRepository::class);
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->registrantRepository->expects($this->any())
            ->method('ofId')
            ->with($this->programCompositionId, $this->registrantId)
            ->willReturn($this->registrant);
        
        $this->service = new RegistrantReject($this->registrantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programCompositionId, $this->registrantId);
    }
    public function test_reject_rejectApplicant()
    {
        $this->registrant->expects($this->once())
            ->method('reject');
        $this->execute();
    }
    public function test_reject_updateRepository()
    {
        $this->registrantRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
}
