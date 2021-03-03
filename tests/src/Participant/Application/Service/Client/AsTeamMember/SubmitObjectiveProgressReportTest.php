<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Participant\ObjectiveRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Tests\src\Participant\Application\Service\Client\AsTeamMember\ObjectiveProgressReportBaseTest;

class SubmitObjectiveProgressReportTest extends ObjectiveProgressReportBaseTest
{

    protected $objectiveRepository;
    protected $objective;
    protected $objectiveId = 'objectiveId';
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->objectiveRepository = $this->buildMockOfInterface(ObjectiveRepository::class);
        $this->objectiveRepository->expects($this->any())
                ->method('ofId')
                ->with($this->objectiveId)
                ->willReturn($this->objective);

        $this->service = new SubmitObjectiveProgressReport(
                $this->teamMemberRepository, $this->teamParticipantRepository, $this->objectiveRepository,
                $this->objectiveProgressReportRepository);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->objectiveId,
                        $this->objectiveProgressReportData);
    }
    public function test_execute_addObjectiveProgressReportSubmittedByTeamMemberToRepository()
    {
        $this->teamMember->expects($this->once())
                ->method('submitObjectiveProgressReport')
                ->with($this->teamParticipant, $this->objective, $this->nextObjectiveProgressReportId, $this->objectiveProgressReportData);
        $this->objectiveProgressReportRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextObjectiveProgressReportId, $this->execute());
    }

}
