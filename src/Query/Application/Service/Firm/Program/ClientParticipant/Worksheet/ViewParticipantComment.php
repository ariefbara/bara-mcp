<?php

namespace Query\Application\Service\Firm\Program\ClientParticipant\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\ParticipantComment;

class ViewParticipantComment
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

    public function showById(string $firmId, string $programId, string $clientId, string $worksheetId,
            string $participantCommentId): ParticipantComment
    {
        return $this->participantCommentRepository->aParticipantCommentOfClientParticipant($firmId, $programId,
                        $clientId, $worksheetId, $participantCommentId);
    }

}
