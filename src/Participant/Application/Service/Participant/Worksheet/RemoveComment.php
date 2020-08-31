<?php

namespace Participant\Application\Service\Participant\Worksheet;

class RemoveComment
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

    public function execute(
            string $firmId, string $clientId, string $programId, string $worksheetId, string $participantCommentId): void
    {
        $this->participantCommentRepository
                ->aParticipantCommentOfClientParticipant($firmId, $clientId, $programId, $worksheetId,
                        $participantCommentId)
                ->remove();
        $this->participantCommentRepository->update();
    }

}
