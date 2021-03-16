<?php

namespace Participant\Application\Service\Client;

use Tests\src\Participant\Application\Service\Client\ObjectiveProgressReportTestBase;

class CancelObjectiveProgressReportSubmissionTest extends ObjectiveProgressReportTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CancelObjectiveProgressReportSubmission(
                $this->clientParticipantRepository, $this->objectiveProgressReportRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->participantId, $this->objectiveProgressReportId);
    }
    public function test_execute_clientParticipantCancelObjectiveProgressReportSubmission()
    {
        $this->clientParticipant->expects($this->once())
                ->method('cancelObjectiveProgressReportSubmission')
                ->with($this->objectiveProgressReport);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
