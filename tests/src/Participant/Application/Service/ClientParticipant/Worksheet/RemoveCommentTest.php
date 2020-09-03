<?php

namespace Participant\Application\Service\ClientParticipant\Worksheet;

use Participant\ {
    Application\Service\Participant\Worksheet\CommentRepository,
    Domain\Model\Participant\Worksheet\Comment
};
use Tests\TestBase;

class RemoveCommentTest extends TestBase
{
    protected $service;
    protected $commentRepository, $comment;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programParticipationId = 'programParticipationId', $worksheetId = 'worksheetId', $commentId = 'commentId';
    protected $message = 'new comment';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->commentRepository->expects($this->any())
                ->method('aCommentInClientParticipantWorksheet')
                ->with($this->firmId, $this->clientId, $this->programParticipationId, $this->worksheetId, $this->commentId)
                ->willReturn($this->comment);
        
        $this->service = new RemoveComment($this->commentRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programParticipationId, $this->worksheetId, $this->commentId);
    }
    public function test_execute_removeComment()
    {
        $this->comment->expects($this->once())
                ->method('remove');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->commentRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
