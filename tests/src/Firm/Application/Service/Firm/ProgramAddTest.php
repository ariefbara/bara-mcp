<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm,
    Domain\Model\Firm\ProgramData
};
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\TestBase;

class ProgramAddTest extends TestBase
{

    protected $service;
    protected $programRepository;
    protected $firmRepository, $firm, $firmId = 'firm-id';
    protected $programData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);

        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId)
                ->willReturn($this->firm);

        $this->service = new ProgramAdd($this->programRepository, $this->firmRepository);

        $this->programData = $this->buildMockOfClass(ProgramData::class);
        $this->programData->expects($this->any())
                ->method('getName')
                ->willReturn('program name');
        $this->programData->expects($this->any())
                ->method('getParticipantTypes')
                ->willReturn([ParticipantTypes::CLIENT_TYPE]);
    }

    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->programData);
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

}
