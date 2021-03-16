<?php

namespace Participant\Application\Service\Client;

use Tests\src\Participant\Application\Service\Client\ObjectiveProgressReportTestBase;

class UpdateObjectiveProgressReportTest extends ObjectiveProgressReportTestBase
{

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateObjectiveProgressReport(
                $this->clientParticipantRepository, $this->objectiveProgressReportRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->participantId, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_execute_clientParticipantUpdateObjectiveProgressReport()
    {
        $this->clientParticipant->expects($this->once())
                ->method('updateObjectiveProgressReport')
                ->with($this->objectiveProgressReport, $this->objectiveProgressReportData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}
