<?php

namespace User\Application\Service\Personnel\Coordinator;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;
use User\Domain\ {
    DependencyModel\Firm\Program\EvaluationPlan,
    DependencyModel\Firm\Program\Participant,
    Model\Personnel\Coordinator
};

class SubmitEvaluationReportTest extends TestBase
{

    protected $coordinator, $coordinatorRepository;
    protected $participant, $participantRepository;
    protected $evaluationPlan, $evaluationPlanRepository;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $coordinatorId = "coordinatorId",
            $participantId = "participantId", $evaluationPlanId = "evaluationPlanId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method("aCoordinatorBelongsToPersonnel")
                ->with($this->firmId, $this->personnelId, $this->coordinatorId)
                ->willReturn($this->coordinator);

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->participantId)
                ->willReturn($this->participant);

        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationPlanRepository = $this->buildMockOfInterface(EvaluationPlanRepository::class);
        $this->evaluationPlanRepository->expects($this->any())
                ->method("ofId")
                ->with($this->evaluationPlanId)
                ->willReturn($this->evaluationPlan);

        $this->service = new SubmitEvaluationReport(
                $this->coordinatorRepository, $this->participantRepository, $this->evaluationPlanRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->personnelId, $this->coordinatorId, $this->participantId, $this->evaluationPlanId, $this->formRecordData);
    }
    public function test_execute_coordinatorSubmitEvaluationReportOfParticipant()
    {
        $this->coordinator->expects($this->once())
                ->method("submitEvaluationReportOfParticipant")
                ->with($this->participant, $this->evaluationPlan, $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
