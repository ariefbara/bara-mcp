<?php

namespace Participant\Application\Service\Participant\Worksheet;

class ReplyParticipantComment
{

    /**
     *
     * @var ParticipantCommentRepository
     */
    protected $participantCommentRepository;

    public function __construct(ParticipantCommentRepository $participantCommentRepository)
    {
        $this->participantCommentRepository = $participantCommentRepository;
    }

    public function execute(string $firmId, string $clientId, string $programId, string $worksheetId,
            string $participantCommentId, string $message): string
    {
        $id = $this->participantCommentRepository->nextIdentity();
        $participantComment = $this->participantCommentRepository
                ->aParticipantCommentOfClientParticipant($firmId, $clientId, $programId, $worksheetId,
                        $participantCommentId)
                ->createReply($id, $message);
        $this->participantCommentRepository->add($participantComment);
        return $id;
    }

}
