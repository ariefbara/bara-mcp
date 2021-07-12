<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\FeedbackForm;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\EvaluationPlanData;
use Firm\Domain\Model\Firm\Program\Mission;
use Tests\TestBase;

class CreateEvaluationPlanTest extends TestBase
{

    protected $evaluationPlanRepository, $nextId = "nextId";
    protected $managerRepository, $manager;
    protected $programRepository, $program;
    protected $feedbackFormRepository, $feedbackForm;
    protected $missionRepository, $mission;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $programId = "programId", 
            $feedbackFormId = "feedbackFormId", $missionId = 'mission-id';
    protected $evaluationPlanData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationPlanRepository = $this->buildMockOfInterface(EvaluationPlanRepository::class);
        $this->evaluationPlanRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);

        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method("aProgramOfId")
                ->with($this->programId)
                ->willReturn($this->program);

        $this->feedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->feedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);
        $this->feedbackFormRepository->expects($this->any())
                ->method("aFeedbackFormOfId")
                ->with($this->feedbackFormId)
                ->willReturn($this->feedbackForm);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->missionRepository->expects($this->any())
                ->method('aMissionOfId')
                ->with($this->missionId)
                ->willReturn($this->mission);

        $this->service = new CreateEvaluationPlan(
                $this->evaluationPlanRepository, $this->managerRepository, $this->programRepository,
                $this->feedbackFormRepository, $this->missionRepository);

        $this->evaluationPlanData = $this->buildMockOfClass(EvaluationPlanData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->managerId, $this->programId, $this->evaluationPlanData,
                        $this->feedbackFormId, $this->missionId);
    }
    public function test_execute_addEvaluationPlanToRepository()
    {
        $this->manager->expects($this->once())
                ->method("createEvaluationPlanInProgram")
                ->with($this->program, $this->nextId, $this->evaluationPlanData, $this->feedbackForm, $this->mission);
        
        $this->evaluationPlanRepository->expects($this->once())
                ->method("add");
        
        $this->execute();
    }
    public function test_execute_emptyMissionId_createEvaluationPlanWithoutMission()
    {
        $this->missionId = null;
        
        $this->manager->expects($this->once())
                ->method("createEvaluationPlanInProgram")
                ->with($this->program, $this->nextId, $this->evaluationPlanData, $this->feedbackForm, null);
        
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
