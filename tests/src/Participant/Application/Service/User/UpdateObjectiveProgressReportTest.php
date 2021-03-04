<?php

namespace Participant\Application\Service\User;

use Tests\src\Participant\Application\Service\User\ObjectiveProgressReportTestBase;

class UpdateObjectiveProgressReportTest extends ObjectiveProgressReportTestBase
{

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateObjectiveProgressReport(
                $this->userParticipantRepository, $this->objectiveProgressReportRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->userId, $this->participantId, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_execute_userParticipantUpdateObjectiveProgressReport()
    {
        $this->userParticipant->expects($this->once())
                ->method('updateObjectiveProgressReport')
                ->with($this->objectiveProgressReport, $this->objectiveProgressReportData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}
