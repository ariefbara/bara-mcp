<?php

namespace Tests\src\Participant\Application\Service\Client;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use PHPUnit\Framework\MockObject\MockObject;

class ObjectiveProgressReportTestBase extends ClientTestBase
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
    protected $nextObjectiveProgressReportId = 'nextObjectiveProgressReportId';
    protected $objectiveProgressReportId = 'objectiveProgressReportId';
    protected $objectiveProgressReportData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        $this->objectiveProgressReportRepository = $this->buildMockOfInterface(ObjectiveProgressReportRepository::class);
        
        $this->objectiveProgressReportRepository->expects($this->any())
                ->method('ofId')
                ->with($this->objectiveProgressReportId)
                ->willReturn($this->objectiveProgressReport);
        $this->objectiveProgressReportRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextObjectiveProgressReportId);
        
        $this->objectiveProgressReportData = $this->buildMockOfClass(ObjectiveProgressReportData::class);
    }
}
