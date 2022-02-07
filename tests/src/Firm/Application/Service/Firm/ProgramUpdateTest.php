<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\ProgramData;
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\TestBase;

class ProgramUpdateTest extends TestBase
{
    protected $firmFileInfoRepository;
    protected $firmFileInfo;
    protected $firmFileInfoId = 'firm-file-info-id';
    protected $service;
    protected $firmId = 'firmId';
    protected $programRepository, $program, $programId = 'programId';
    protected $programRequest;
    protected $programData;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->firmFileInfo = $this->buildMockOfClass(FirmFileInfo::class);
        $this->firmFileInfoRepository = $this->buildMockOfInterface(FirmFileInfoRepository::class);
        $this->firmFileInfoRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmFileInfoId)
                ->willReturn($this->firmFileInfo);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->programId)
            ->willReturn($this->program);
        
        $this->service = new ProgramUpdate($this->programRepository, $this->firmFileInfoRepository);
        
        $this->programRequest = new ProgramRequest('name', null, true, $this->firmFileInfoId, 'incubation');
        $this->programRequest->addParticipantType(ParticipantTypes::CLIENT_TYPE);
        
        $this->programData = new ProgramData('name', null, true, $this->firmFileInfo, 'incubation');
        $this->programData->addParticipantType(ParticipantTypes::CLIENT_TYPE);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->programId, $this->programRequest);
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
    public function test_update_emptyIllustration()
    {
        $this->programRequest = new ProgramRequest('name', null, true, null, 'incubation');
        $this->execute();
        $this->markAsSuccess();
    }
}
