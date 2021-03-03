<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Tests\src\Participant\Application\Service\Client\AsTeamMember\ObjectiveProgressReportBaseTest;

class UpdateObjectiveProgressReportTest extends ObjectiveProgressReportBaseTest
{

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateObjectiveProgressReport(
                $this->teamMemberRepository, $this->teamParticipantRepository, $this->objectiveProgressReportRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_execute_teamMemberUpdateObjectiveProgressReport()
    {
        $this->teamMember->expects($this->once())
                ->method('updateObjectiveProgressReport')
                ->with($this->teamParticipant, $this->objectiveProgressReport, $this->objectiveProgressReportData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}
