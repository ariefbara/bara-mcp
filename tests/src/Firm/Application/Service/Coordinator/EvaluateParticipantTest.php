<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\{
    Coordinator,
    Participant\EvaluationData
};
use Tests\TestBase;

class EvaluateParticipantTest extends TestBase
{

    protected $participantRepository;
    protected $coordinatorRepository, $coordinator;
    protected $evaluationPlanRepository;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $programId = "programId",
            $participantId = "participantId", $evaluationPlanId = "evaluationPlanId";
    protected $evaluationData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method("aCoordinatorCorrespondWithProgram")
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->coordinator);

        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->evaluationPlanRepository = $this->buildMockOfInterface(EvaluationPlanRepository::class);

        $this->service = new EvaluateParticipant(
                $this->coordinatorRepository, $this->participantRepository, $this->evaluationPlanRepository);
        $this->evaluationData = $this->buildMockOfClass(EvaluationData::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->personnelId, $this->programId, $this->participantId, $this->evaluationPlanId,
                $this->evaluationData);
    }
    public function test_execute_coordinatorEvaluateParticipant()
    {
        $this->participantRepository->expects($this->once())->method("ofId")->with($this->participantId);
        $this->evaluationPlanRepository->expects($this->once())->method("ofId")->with($this->evaluationPlanId);
        
        $this->coordinator->expects($this->once())
                ->method("evaluateParticipant");
        $this->execute();
    }
    public function test_execute_updateParticipantRepository()
    {
        $this->participantRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
