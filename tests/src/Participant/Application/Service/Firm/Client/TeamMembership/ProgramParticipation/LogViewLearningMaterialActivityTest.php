<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\ParticipantRepository,
    Application\Service\Participant\ViewLearningMaterialActivityLogRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant
};
use Tests\TestBase;

class LogViewLearningMaterialActivityTest extends TestBase
{
    protected $viewLearningMaterialActivityLogRepository, $nextId = "nextId";
    protected $teamMembershipRepository, $teamMembership;
    protected $participantRepository, $participant;
    protected $service;
    protected $teamMemberId = "memberId", $participantId = "participantId", $learningMaterialId = "learningMaterialId";
    
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->viewLearningMaterialActivityLogRepository = $this->buildMockOfInterface(ViewLearningMaterialActivityLogRepository::class);
        $this->viewLearningMaterialActivityLogRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("aTeamMembershipById")
                ->with($this->teamMemberId)
                ->willReturn($this->teamMembership);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->participantId)
                ->willReturn($this->participant);
        
        $this->service = new LogViewLearningMaterialActivity(
                $this->viewLearningMaterialActivityLogRepository, $this->teamMembershipRepository, $this->participantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->teamMemberId, $this->participantId, $this->learningMaterialId);
    }
    
    public function test_execute_addViewLearningMaterialActivityLogToRepository()
    {
        $this->teamMembership->expects($this->once())
                ->method("logViewLearningMaterialActivity")
                ->with($this->nextId, $this->participant, $this->learningMaterialId);
        $this->execute();
    }
}
