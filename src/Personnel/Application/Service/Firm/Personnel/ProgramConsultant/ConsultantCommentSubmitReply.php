<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\ {
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
            string $firmId, string $personnelId, string $programConsultantId, string $participantId,
            string $worksheetId, string $commentId, string $message): string
    {
        $id = $this->consultantCommentRepository->nextIdentity();
        $comment = $this->commentRepository->aCommentInProgramWorksheetWhereConsultantInvolved($firmId, $personnelId,
                $programConsultantId, $participantId, $worksheetId, $commentId);
        
        $programConsultation = $this->programConsultantRepository->ofId($firmId, $personnelId, $programConsultantId);
        $consultantComment = $programConsultation->submitReplyOnWorksheetComment($id, $comment, $message);
        
        $this->consultantCommentRepository->add($consultantComment);
        
        $this->dispatcher->dispatch($programConsultation);
        
        return $id;
    }

}
