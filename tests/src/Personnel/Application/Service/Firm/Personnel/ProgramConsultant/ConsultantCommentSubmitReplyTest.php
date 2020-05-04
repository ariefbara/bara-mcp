<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Application\Service\Firm\Program\Participant\Worksheet\CommentRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};
use Query\Application\Service\Firm\Personnel\PersonnelCompositionId;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ConsultantCommentSubmitReplyTest extends TestBase
{

    protected $consultantCommentRepository, $nextIdentity = 'nextIdentity';
    protected $programConsultantRepository, $programConsultant, $programConsultantId = 'programConsultantId';
    protected $commentRepository, $comment, $participantId = 'participantId', $worksheetId = 'worksheetId', $commentId = 'commentId';
    protected $dispatcher;
    protected $service;
    protected $personnelCompositionId;
    protected $message = 'message';
    protected $repliedComment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantCommentRepository = $this->buildMockOfInterface(ConsultantCommentRepository::class);
        $this->consultantCommentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextIdentity);
        $this->programConsultantRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ConsultantCommentSubmitReply(
                $this->consultantCommentRepository, $this->programConsultantRepository, $this->commentRepository,
                $this->dispatcher);

        $this->personnelCompositionId = $this->buildMockOfClass(PersonnelCompositionId::class);
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->repliedComment = $this->buildMockOfClass(Comment::class);
    }

    protected function execute()
    {
        $this->programConsultantRepository->expects($this->once())
                ->method('ofId')
                ->with($this->personnelCompositionId, $this->programConsultantId)
                ->willReturn($this->programConsultant);
        $this->commentRepository->expects($this->once())
                ->method('aCommentInProgramWorksheetWhereConsultantInvolved')
                ->with($this->personnelCompositionId, $this->programConsultantId, $this->participantId,
                        $this->worksheetId, $this->commentId)
                ->willReturn($this->comment);
        $this->comment->expects($this->once())
                ->method('createReply')
                ->with($this->nextIdentity, $this->message)
                ->willReturn($this->repliedComment);

        return $this->service->execute(
                        $this->personnelCompositionId, $this->programConsultantId, $this->participantId,
                        $this->worksheetId, $this->commentId, $this->message);
    }

    public function test_execute_addConsultantCommentToRepository()
    {
        $consultantComment = new ConsultantComment($this->programConsultant, $this->nextIdentity, $this->repliedComment);
        $this->consultantCommentRepository->expects($this->once())
                ->method('add')
                ->with($consultantComment);
        $this->execute();
    }

    public function test_execute_dispatchConsultantCommentToDispatcher()
    {
        $consultantComment = new ConsultantComment($this->programConsultant, $this->nextIdentity, $this->repliedComment);
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
