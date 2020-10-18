<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\ {
    Application\Service\User\ProgramParticipationRepository,
    Domain\Model\User\UserParticipant,
    Domain\Service\LearningMaterialFinder
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ViewLearningMaterialDetailTest extends TestBase
{
    protected $userProgramParticipationRepository, $userProgramParticipation;
    protected $learningMaterialFinder;
    protected $dispatcher;
    protected $service;
    protected $userId = "userId", $programId = "programId", $learningMaterialId = "learningMaterialId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userProgramParticipation = $this->buildMockOfClass(UserParticipant::class);
        $this->userProgramParticipationRepository = $this->buildMockOfInterface(ProgramParticipationRepository::class);
        $this->userProgramParticipationRepository->expects($this->any())
                ->method("aProgramParticipationOfUserCorrespondWithProgram")
                ->with($this->userId, $this->programId)
                ->willReturn($this->userProgramParticipation);
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new ViewLearningMaterialDetail(
                $this->userProgramParticipationRepository, $this->learningMaterialFinder, $this->dispatcher);
    }
    protected function execute()
    {
        $this->service->execute($this->userId, $this->programId, $this->learningMaterialId);
    }
    public function test_execute_returnUserParticipantsViewLearningMaterialResult()
    {
        $this->userProgramParticipation->expects($this->once())
                ->method("viewLearningMaterial")
                ->with($this->learningMaterialFinder, $this->learningMaterialId);
        $this->execute();
    }
    public function test_execute_dispatchUserProgramParticipation()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->userProgramParticipation);
        $this->execute();
    }
}
