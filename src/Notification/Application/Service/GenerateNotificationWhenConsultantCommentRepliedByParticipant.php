<?php

namespace Notification\Application\Service;

class GenerateNotificationWhenConsultantCommentRepliedByParticipant
{
    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;
    
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }
    
    public function execute(string $commentId): void
    {
        $this->commentRepository->ofId($commentId)
                ->generateNotificationsForRepliedConsultantComment();
        $this->commentRepository->update();
    }

}
