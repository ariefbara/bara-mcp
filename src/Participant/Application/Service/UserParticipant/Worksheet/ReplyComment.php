<?php

namespace Participant\Application\Service\UserParticipant\Worksheet;

use Participant\Application\Service\ {
    Participant\Worksheet\CommentRepository,
    UserParticipantRepository
};
use Resources\Application\Event\Dispatcher;

class ReplyComment
{
    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     *
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(
            CommentRepository $commentRepository, UserParticipantRepository $userParticipantRepository, Dispatcher $dispatcher)
    {
        $this->commentRepository = $commentRepository;
        $this->userParticipantRepository = $userParticipantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $userId, string $userParticipantId, string $worksheetId, string $commentId, string $message): string
    {
        $id = $this->commentRepository->nextIdentity();
        $userParticipat = $this->userParticipantRepository->ofId($userId, $userParticipantId);
        $toReply = $this->commentRepository->aCommentInUserParticipantWorksheet(
                $userId, $userParticipantId, $worksheetId, $commentId);
        
        $comment = $userParticipat->replyComment($id, $toReply, $message);
        $this->commentRepository->add($comment);
        
        $this->dispatcher->dispatch($comment);
        return $id;
    }
}
