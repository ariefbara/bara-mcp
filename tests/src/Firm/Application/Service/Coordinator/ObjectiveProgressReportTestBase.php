<?php

namespace Tests\src\Firm\Application\Service\Coordinator;

use Firm\Application\Service\Coordinator\ObjectiveProgressReportRepository;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use PHPUnit\Framework\MockObject\MockObject;

class ObjectiveProgressReportTestBase extends CoordinatorTestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $objectiveProgressReportRepository;
    /**
     * 
     * @var MockObject
     */
    protected $objectiveProgressReport;
    protected $objectiveProgressReportId = 'objectiveProgressReportId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        $this->objectiveProgressReportRepository = $this->buildMockOfInterface(ObjectiveProgressReportRepository::class);
        $this->objectiveProgressReportRepository->expects($this->any())
                ->method('ofId')
                ->with($this->objectiveProgressReportId)
                ->willReturn($this->objectiveProgressReport);
    }
}
