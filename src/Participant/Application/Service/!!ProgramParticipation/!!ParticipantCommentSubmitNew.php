<?php

namespace User\Application\Service\User\ProgramParticipation;

use User\{
    Application\Service\User\ProgramParticipationRepository,
    Domain\Model\User\ProgramParticipation\ParticipantComment,
    Domain\Model\User\ProgramParticipation\Worksheet\Comment
};

class ParticipantCommentSubmitNew
{

    /**
     *
     * @var ParticipantCommentRepository
     */
    protected $participantCommentRepository;

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    function __construct(ParticipantCommentRepository $participantCommentRepository,
            ProgramParticipationRepository $programParticipationRepository, WorksheetRepository $worksheetRepository)
    {
        $this->participantCommentRepository = $participantCommentRepository;
        $this->programParticipationRepository = $programParticipationRepository;
        $this->worksheetRepository = $worksheetRepository;
    }

    public function execute(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId, string $message): string
    {
        $programParticipation = $this->programParticipationRepository->ofId(
                $programParticipationCompositionId->getUserId(),
                $programParticipationCompositionId->getProgramParticipationId());
        $id = $this->participantCommentRepository->nextIdentity();
        $worksheet = $this->worksheetRepository->ofId($programParticipationCompositionId, $worksheetId);
        $comment = Comment::createNew($worksheet, $id, $message);

        $participantComment = new ParticipantComment($programParticipation, $id, $comment);
        $this->participantCommentRepository->add($participantComment);
        return $id;
    }

}
