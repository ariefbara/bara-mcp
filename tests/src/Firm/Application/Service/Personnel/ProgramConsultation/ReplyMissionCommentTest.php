<?php

namespace Firm\Application\Service\Personnel\ProgramConsultant;

use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Tests\src\Firm\Application\Service\Personnel\ProgramConsultation\MissionCommentTestBase;

class ReplyMissionCommentTest extends MissionCommentTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReplyMissionComment($this->consultantRepository, $this->missionCommentRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->personnelId, $this->programId, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_execute_addReplyCommentFromConsultantToRepository()
    {
        $this->consultant->expects($this->once())
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
