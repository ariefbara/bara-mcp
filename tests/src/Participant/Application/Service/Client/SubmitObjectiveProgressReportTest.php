<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\Participant\ObjectiveRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Tests\src\Participant\Application\Service\Client\ObjectiveProgressReportTestBase;


class SubmitObjectiveProgressReportTest extends ObjectiveProgressReportTestBase
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
                $this->clientParticipantRepository, $this->objectiveRepository, $this->objectiveProgressReportRepository);
    }

    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->participantId, $this->objectiveId,$this->objectiveProgressReportData);
    }
    public function test_execute_addObjectiveProgressReportSubmittedByClientParticipantToRepository()
    {
        $this->clientParticipant->expects($this->once())
                ->method('submitObjectiveProgressReport')
                ->with($this->objective, $this->nextObjectiveProgressReportId, $this->objectiveProgressReportData);
        $this->objectiveProgressReportRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextObjectiveProgressReportId, $this->execute());
    }

}
