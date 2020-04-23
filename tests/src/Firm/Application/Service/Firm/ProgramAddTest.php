<?php

namespace Firm\Application\Service\Firm;

use Firm\{
    Application\Service\FirmRepository,
    Domain\Model\Firm,
    Domain\Model\Firm\ProgramData
};
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
    }

    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->programData);
    }

    function test_add_addProgramToRepository()
    {
        $this->programRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }

}
