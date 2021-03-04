<?php

namespace Participant\Application\Service\User;

use Tests\src\Participant\Application\Service\User\ObjectiveProgressReportTestBase;

class CancelObjectiveProgressReportSubmissionTest extends ObjectiveProgressReportTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CancelObjectiveProgressReportSubmission(
                $this->userParticipantRepository, $this->objectiveProgressReportRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->userId, $this->participantId, $this->objectiveProgressReportId);
    }
    public function test_execute_userParticipantCancelObjectiveProgressReportSubmission()
    {
        $this->userParticipant->expects($this->once())
                ->method('cancelObjectiveProgressReportSubmission')
                ->with($this->objectiveProgressReport);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
