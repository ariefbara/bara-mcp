<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\ {
    Application\Service\Client\ProgramParticipation\Worksheet\CommentRepository,
    Application\Service\Client\ProgramParticipation\Worksheet\WorksheetCompositionId,
    Application\Service\Client\ProgramParticipationRepository,
    Domain\Model\Client\ProgramParticipation\ParticipantComment
};

class ParticipantCommentSubmitReply
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
     * @var CommentRepository
     */
    protected $commentRepoository;

    function __construct(
            ParticipantCommentRepository $participantCommentRepository,
            ProgramParticipationRepository $programParticipationRepository, CommentRepository $commentRepoository)
    {
        $this->participantCommentRepository = $participantCommentRepository;
        $this->programParticipationRepository = $programParticipationRepository;
        $this->commentRepoository = $commentRepoository;
    }

    public function execute(WorksheetCompositionId $worksheetCompositionId, string $commentId, string $message): ParticipantComment
    {
        $programParticipation = $this->programParticipationRepository->ofId(
                $worksheetCompositionId->getClientId(), $worksheetCompositionId->getProgramParticipationId());
        $id = $this->participantCommentRepository->nextIdentity();
        $comment = $this->commentRepoository->ofId($worksheetCompositionId, $commentId)->createReply($id, $message);
        $participantComment = new ParticipantComment($programParticipation, $id, $comment);
        $this->participantCommentRepository->add($participantComment);
        return $participantComment;
    }

}
