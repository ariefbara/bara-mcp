<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment;
use Tests\TestBase;

class GenerateCommentSubmittedByConsultantNotificationTest extends TestBase
{
    protected $commentRepository, $comment;
    protected $service;
    protected $commentId = "commentId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->commentRepository->expects($this->once())
                ->method("ofId")
                ->with($this->commentId)
                ->willReturn($this->comment);
        
        $this->service = new GenerateCommentSubmittedByConsultantNotification($this->commentRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->commentId);
    }
    
    public function test_execute_addNotificationTriggeredByConsultant()
    {
        $this->comment->expects($this->once())
                ->method("generateNotificationsTriggeredByConsultant");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->commentRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
