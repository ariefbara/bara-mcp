<?php

namespace Participant\Application\Service\ClientParticipant\Worksheet;

use Participant\Application\Service\ {
    ClientParticipantRepository,
    Participant\Worksheet\CommentRepository
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
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(CommentRepository $commentRepository,
            ClientParticipantRepository $clientParticipantRepository, Dispatcher $dispatcher)
    {
        $this->commentRepository = $commentRepository;
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $commentId, string $message): string
    {
        $id = $this->commentRepository->nextIdentity();
        $clientParticipat = $this->clientParticipantRepository->ofId($firmId, $clientId, $programParticipationId);
        $toReply = $this->commentRepository->aCommentInClientParticipantWorksheet(
                $firmId, $clientId, $programParticipationId, $worksheetId, $commentId);
        
        $comment = $clientParticipat->replyComment($id, $toReply, $message);
        $this->commentRepository->add($comment);
        
        $this->dispatcher->dispatch($comment);
        return $id;
    }

}
