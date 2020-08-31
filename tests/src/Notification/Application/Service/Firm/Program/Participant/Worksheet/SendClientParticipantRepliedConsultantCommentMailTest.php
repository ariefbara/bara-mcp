<?php

namespace Notification\Application\Service\Firm\Program\Participant\Worksheet;

use Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment;
use Resources\Application\Service\Mailer;
use Tests\TestBase;

class SendClientParticipantRepliedConsultantCommentMailTest extends TestBase
{
    protected $service;
    protected $commentRepository, $comment;
    protected $mailer;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programParticipationId = 'programParticipationId', $worksheetId = 'worksheetId', $commentId = 'commentId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->commentRepository->expects($this->any())
                ->method('aCommentInClientParticipantWorksheet')
                ->with($this->firmId, $this->clientId, $this->programParticipationId, $this->worksheetId, $this->commentId)
                ->willReturn($this->comment);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
        
        $this->service = new SendClientParticipantRepliedConsultantCommentMail($this->commentRepository, $this->mailer);
    }
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programParticipationId, $this->worksheetId, $this->commentId);
    }
    
    public function test_execute_executeCommentssendMailToConsultantWhoseCommentBeingReplied()
    {
        $this->comment->expects($this->once())
                ->method('sendMailToConsultantWhoseCommentBeingReplied')
                ->with($this->mailer);
        $this->execute();
    }
    
}
