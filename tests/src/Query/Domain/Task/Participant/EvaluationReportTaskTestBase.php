<?php

namespace Tests\src\Query\Domain\Task\Participant;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;

class EvaluationReportTaskTestBase extends ParticipantTaskTestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $evaluationReportRepository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
    }
}
