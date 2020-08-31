<?php

namespace Client\Application\Service\Client\ProgramParticipation\Worksheet\Comment;

use Client\ {
    Application\Service\Client\ProgramParticipation\Worksheet\CommentRepository,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment\CommentNotification
};
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Tests\TestBase;

class CommentNotificationFromConsultantAddTest extends TestBase
{
    protected $commentNotificationRepository, $nextId = 'id';
    protected $commentRepository;
    protected $service;
    protected $comment;
    protected $programConsultantCompositionId, $consultantCommentId = 'consultantCommentId', $message = 'message';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->commentNotificationRepository = $this->buildMockOfInterface(CommentNotificationRepository::class);
        $this->programConsultantCompositionId = $this->buildMockOfClass(ProgramConsultantCompositionId::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->service = new CommentNotificationFromConsultantAdd($this->commentNotificationRepository, $this->commentRepository);
        
        $this->comment = $this->buildMockOfClass(Comment::class);
    }
    
    public function test_execute_addCommentNotificationToRepository()
    {
        
        $this->comment->expects($this->once())
                ->method('createCommentNotification')
                ->with($this->nextId, $this->message)
                ->willReturn($commentNotification = $this->buildMockOfClass(CommentNotification::class));
        $this->commentRepository->expects($this->once())
                ->method('aCommentFromConsultant')
                ->with($this->programConsultantCompositionId, $this->consultantCommentId)
                ->willReturn($this->comment);
        
        $this->commentNotificationRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        $this->commentNotificationRepository->expects($this->once())
                ->method('add')
                ->with($commentNotification);
        
        $this->service->execute($this->programConsultantCompositionId, $this->consultantCommentId, $this->message);
    }
}
