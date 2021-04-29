<?php

use Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\SubmitMissionComment;
use Firm\Application\Service\Firm\Program\MissionRepository;
use Firm\Domain\Model\Firm\Program\Mission;
use Tests\src\Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\MissionCommentTestBase;

class SubmitMissionCommentTest extends MissionCommentTestBase
{

    protected $missionRepository;
    protected $mission;
    protected $service;
    protected $missionId = 'missionId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->missionRepository->expects($this->any())
                ->method('aMissionOfId')
                ->with($this->missionId)
                ->willReturn($this->mission);
        
        $this->service = new SubmitMissionComment(
                $this->teamMemberRepository, $this->teamParticipantRepository, $this->missionRepository,
                $this->missionCommentRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->programId, $this->missionId, $this->missionCommentData);
    }
    public function test_execute_addMissionCommentSubmittedByTeamMemberToRepository()
    {
        $this->teamMember->expects($this->once())
                ->method('submitCommentInMission')
                ->with($this->teamParticipant, $this->mission, $this->missionCommentNextId, $this->missionCommentData)
                ->willReturn($this->missionComment);
        
        $this->missionCommentRepository->expects($this->once())
                ->method('add')
                ->with($this->missionComment);
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->missionCommentNextId, $this->execute());
    }

}
