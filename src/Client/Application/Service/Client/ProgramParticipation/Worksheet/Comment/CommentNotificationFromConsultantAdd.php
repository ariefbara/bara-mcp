<?php

namespace Client\Application\Service\Client\ProgramParticipation\Worksheet\Comment;

use Client\Application\Service\Client\ProgramParticipation\Worksheet\CommentRepository;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;

class CommentNotificationFromConsultantAdd
{

    /**
     *
     * @var CommentNotificationRepository
     */
    protected $commentNotificationRepository;

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    function __construct(CommentNotificationRepository $commentNotificationRepository,
            CommentRepository $commentRepository)
    {
        $this->commentNotificationRepository = $commentNotificationRepository;
        $this->commentRepository = $commentRepository;
    }

    public function execute(
            ProgramConsultantCompositionId $programConsultantCompositionid, string $consultantCommentId, string $message): void
    {
        $comment = $this->commentRepository->aCommentFromConsultant(
                $programConsultantCompositionid, $consultantCommentId);
        $id = $this->commentNotificationRepository->nextIdentity();
        $commentNotification = $comment->createCommentNotification($id, $message);
        $this->commentNotificationRepository->add($commentNotification);
    }

}
