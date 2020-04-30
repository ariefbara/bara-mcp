<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\{
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Application\Service\Firm\Program\Participant\Worksheet\CommentRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment
};
use Resources\Application\Event\Dispatcher;

class ConsultantCommentSubmitReply
{

    /**
     *
     * @var ConsultantCommentRepository
     */
    protected $consultantCommentRepository;

    /**
     *
     * @var ProgramConsultantRepository
     */
    protected $programConsultantRepository;

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            ConsultantCommentRepository $consultantCommentRepository,
            ProgramConsultantRepository $programConsultantRepository, CommentRepository $commentRepository,
            Dispatcher $dispatcher)
    {
        $this->consultantCommentRepository = $consultantCommentRepository;
        $this->programConsultantRepository = $programConsultantRepository;
        $this->commentRepository = $commentRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            PersonnelCompositionId $personnelCompositionId, string $programConsultantId, string $participantId,
            string $worksheetId, string $commentId, string $message): ConsultantComment
    {
        $programConsultant = $this->programConsultantRepository->ofId($personnelCompositionId, $programConsultantId);
        $id = $this->consultantCommentRepository->nextIdentity();
        $comment = $this->commentRepository->aCommentInProgramWorksheetWhereConsultantInvolved(
                        $personnelCompositionId, $programConsultantId, $participantId, $worksheetId, $commentId)
                ->createReply($id, $message);

        $consultantComment = new ConsultantComment($programConsultant, $id, $comment);
        $this->consultantCommentRepository->add($consultantComment);
        
        $this->dispatcher->dispatch($consultantComment);
        return $consultantComment;
    }

}
