<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Consultant;
use Tests\TestBase;

class ConsultantRemoveTest extends TestBase
{
    protected $service;
    protected $consultantRepository, $consultant, $programCompositionId, $consultantId = 'consultant-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programCompositionId = $this->buildMockOfClass(ProgramCompositionId::class);
        
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->any())
            ->method('ofId')
            ->with($this->programCompositionId, $this->consultantId)
            ->willReturn($this->consultant);
        
        $this->service = new ConsultantRemove($this->consultantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programCompositionId, $this->consultantId);
    }
    
    public function test_execute_removeConsultant()
    {
        $this->consultant->expects($this->once())
            ->method('remove');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultantRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
}
