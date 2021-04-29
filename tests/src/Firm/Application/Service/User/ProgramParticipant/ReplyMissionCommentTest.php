<?php

namespace Firm\Application\Service\User\ProgramParticipant;

use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Tests\src\Firm\Application\Service\User\ProgramParticipant\MissionCommentTestBase;

class ReplyMissionCommentTest extends MissionCommentTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReplyMissionComment($this->userParticipantRepository, $this->missionCommentRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->userId, $this->programId, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_execute_addReplyCommentFromUserParticipantToRepository()
    {
        $this->userParticipant->expects($this->once())
                ->method('replyMissionComment')
                ->with($this->missionComment, $this->missionCommentNextId, $this->missionCommentData)
                ->willReturn($reply = $this->buildMockOfClass(MissionComment::class));
        $this->missionCommentRepository->expects($this->once())
                ->method('add')
                ->with($reply);
        $this->execute();
    }
    public function test_execute_returnNextMissionCommentId()
    {
        $this->assertEquals($this->missionCommentNextId, $this->execute());
    }
}
