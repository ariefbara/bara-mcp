<?php

namespace Client\Application\Service\Client\ProgramParticipation;

class ParticipantCommentRemove
{

    /**
     *
     * @var ParticipantCommentRepository
     */
    protected $participantCommentRepository;

    function __construct(ParticipantCommentRepository $participantCommentRepository)
    {
        $this->participantCommentRepository = $participantCommentRepository;
    }

    public function execute(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participantCommentId): void
    {
        $this->participantCommentRepository->ofId($programParticipationCompositionId, $participantCommentId)
                ->remove();
        $this->participantCommentRepository->update();
    }

}
