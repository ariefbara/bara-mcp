<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\ {
    Program,
    ProgramData
};
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\TestBase;

class ProgramUpdateTest extends TestBase
{
    protected $service;
    protected $firmId = 'firmId';
    protected $programRepository, $program, $programId = 'programId';
    protected $programData;


    protected function setUp(): void
    {
        parent::setUp();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->programId)
            ->willReturn($this->program);
        
        $this->service = new ProgramUpdate($this->programRepository);
        
        $this->programData = $this->buildMockOfClass(ProgramData::class);
        $this->programData->expects($this->any())
                ->method('getName')
                ->willReturn('new program name');
        $this->programData->expects($this->any())
                ->method('getParticipantTypes')
                ->willReturn([ParticipantTypes::USER_TYPE]);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->programId, $this->programData);
    }
    
    function test_update_updateProgramme() {
        $this->program->expects($this->once())
            ->method('update')
            ->with($this->programData);
        $this->execute();
    }
    function test_update_updateRepository() {
        $this->programRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
}
