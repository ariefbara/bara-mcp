<?php

namespace Participant\Application\Service\UserParticipant\Worksheet;

use Participant\ {
    Application\Service\Participant\Worksheet\CommentRepository,
    Application\Service\Participant\WorksheetRepository,
    Domain\Model\Participant\Worksheet,
    Domain\Model\Participant\Worksheet\Comment
};
use Tests\TestBase;

class SubmitNewCommentTest extends TestBase
{
    protected $service;
    protected $commentRepository, $nextId = 'nextId';
    protected $worksheetRepository, $worksheet;
    
    protected $userId = 'userId', $userParticipantId = 'userParticipantId', $worksheetId = 'worksheetId';
    
    protected $message = 'new comment';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->commentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfClass(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('aWorksheetBelongsToUserParticipant')
                ->with($this->userId, $this->userParticipantId, $this->worksheetId)
                ->willReturn($this->worksheet);
        
        $this->service = new SubmitNewComment($this->commentRepository, $this->worksheetRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->userId, $this->userParticipantId, $this->worksheetId, $this->message);
    }
    public function test_execute_addCommentToRepository()
    {
        $this->worksheet->expects($this->once())
                ->method('createComment')
                ->with($this->nextId, $this->message, null)
                ->willReturn($comment = $this->buildMockOfClass(Comment::class));
        
        $this->commentRepository->expects($this->once())
                ->method('add')
                ->with($comment);
        
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
