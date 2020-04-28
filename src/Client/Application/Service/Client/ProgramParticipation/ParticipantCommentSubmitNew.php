<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\{
    Application\Service\Client\ProgramParticipationRepository,
    Domain\Model\Client\ProgramParticipation\ParticipantComment,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment
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
            ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId, string $message): ParticipantComment
    {
        $programParticipation = $this->programParticipationRepository->ofId(
                $programParticipationCompositionId->getClientId(),
                $programParticipationCompositionId->getProgramParticipationId());
        $id = $this->participantCommentRepository->nextIdentity();
        $worksheet = $this->worksheetRepository->ofId($programParticipationCompositionId, $worksheetId);
        $comment = Comment::createNew($worksheet, $id, $message);

        $participantComment = new ParticipantComment($programParticipation, $id, $comment);
        $this->participantCommentRepository->add($participantComment);
        return $participantComment;
    }

}
