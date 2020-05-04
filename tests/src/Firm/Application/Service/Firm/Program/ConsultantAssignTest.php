<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\{
    Application\Service\Firm\PersonnelRepository,
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Program
};
use Tests\TestBase;

class ConsultantAssignTest extends TestBase
{

    protected $service;
    protected $firmId = 'firm-id';
    protected $programRepository, $program, $programId = 'program-id';
    protected $personnelRepository, $personnel, $personnelId = 'personnel-id';

    protected function setUp(): void
    {
        parent::setUp();

        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfClass(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId)
                ->willReturn($this->program);

        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnelRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId)
                ->willReturn($this->personnel);

        $this->service = new ConsultantAssign($this->programRepository, $this->personnelRepository);
    }

    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->programId, $this->personnelId);
    }

    public function test_execute_executeCohortsAssignConsultantMethod()
    {
        $this->program->expects($this->once())
                ->method('assignPersonnelAsConsultant')
                ->with($this->personnel);
        $this->execute();
    }

    public function test_execute_updateCohortRepository()
    {
        $this->programRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

    public function test_execute_returnIdResultOfProgramsAssignPersonnelAsConsultant()
    {
        $this->program->expects($this->once())
                ->method('assignPersonnelAsConsultant')
                ->with($this->personnel)
                ->willReturn($id = 'id');
        $this->assertEquals($id, $this->execute());
    }

}
