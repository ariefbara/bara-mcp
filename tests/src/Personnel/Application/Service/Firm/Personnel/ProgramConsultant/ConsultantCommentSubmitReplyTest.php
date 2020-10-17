<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Application\Service\Firm\Program\Participant\Worksheet\CommentRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ConsultantCommentSubmitReplyTest extends TestBase
{
    protected $consultantCommentRepository, $nextIdentity = 'nextIdentity';
    protected $programConsultantRepository, $programConsultant;
    protected $dispatcher;
    protected $service;
    protected $commentRepository, $comment;
    
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programConsultationId = 'programConsultationId',
            $participantId = 'participantId', $worksheetId = "worksheetId", $commentId = 'commentId';
    protected $message = 'message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->programConsultantRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->programConsultantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId, $this->programConsultationId)
                ->willReturn($this->programConsultant);
        
        $this->consultantCommentRepository = $this->buildMockOfInterface(ConsultantCommentRepository::class);
        $this->consultantCommentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextIdentity);

        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->commentRepository->expects($this->any())
                ->method('aCommentInProgramWorksheetWhereConsultantInvolved')
                ->with($this->firmId, $this->personnelId, $this->programConsultationId, $this->participantId, $this->worksheetId, $this->commentId)
                ->willReturn($this->comment);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ConsultantCommentSubmitReply(
                $this->consultantCommentRepository, $this->programConsultantRepository, $this->commentRepository,
                $this->dispatcher);
    }

    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->personnelId, $this->programConsultationId, $this->participantId, $this->worksheetId, $this->commentId, $this->message);
    }

    public function test_execute_addConsultantCommentToRepository()
    {
        $this->programConsultant->expects($this->once())
                ->method('submitReplyOnWorksheetComment')
                ->with($this->nextIdentity, $this->comment, $this->message);
        
        $this->consultantCommentRepository->expects($this->once())
                ->method('add');
        
        $this->execute();
    }
    public function test_execute_dispatchConsultantCommentToDispatcher()
    {
        $this->programConsultant->expects($this->once())
                ->method('submitReplyOnWorksheetComment')
                ->willReturn($consultantComment = $this->buildMockOfClass(ConsultantComment::class));
        
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($consultantComment);
        
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextIdentity, $this->execute());
    }

}
