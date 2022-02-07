<?php

namespace Firm\Application\Service\Firm;

use Firm\Application\Service\FirmRepository;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\ProgramData;
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\TestBase;

class ProgramAddTest extends TestBase
{
    protected $firmFileInfoRepository;
    protected $firmFileInfo;
    protected $firmFileInfoId = 'firm-file-info-id';
    protected $service;
    protected $programRepository;
    protected $firmRepository, $firm, $firmId = 'firm-id';
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

        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);

        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId)
                ->willReturn($this->firm);

        $this->service = new ProgramAdd($this->programRepository, $this->firmRepository, $this->firmFileInfoRepository);
        
        $this->programRequest = new ProgramRequest('name', 'description', true, $this->firmFileInfoId, 'incubation');
        $this->programRequest->addParticipantType(ParticipantTypes::CLIENT_TYPE);
        
        $this->programData = new ProgramData('name', 'description', true, $this->firmFileInfo, 'incubation');
        $this->programData->addParticipantType(ParticipantTypes::CLIENT_TYPE);
//
//        $this->programData = $this->buildMockOfClass(ProgramData::class);
//        $this->programData->expects($this->any())
//                ->method('getName')
//                ->willReturn('program name');
//        $this->programData->expects($this->any())
//                ->method('getParticipantTypes')
//                ->willReturn([ParticipantTypes::CLIENT_TYPE]);
    }

    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->programRequest);
    }

    function test_execute_addProgramToRepository()
    {
        $this->programRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_returnNewId()
    {
        $this->programRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($id = 'id');
        $this->assertEquals($id, $this->execute());
        
    }
    public function test_execute_emptyIllustration()
    {
        $this->programRequest = new ProgramRequest('name', 'description', true, null, 'incubation');
        $this->execute();
        $this->markAsSuccess();
    }

}
