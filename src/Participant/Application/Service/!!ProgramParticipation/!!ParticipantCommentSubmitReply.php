<?php

namespace User\Application\Service\User\ProgramParticipation;

use User\ {
    Application\Service\User\ProgramParticipation\Worksheet\CommentRepository,
    Application\Service\User\ProgramParticipation\Worksheet\WorksheetCompositionId,
    Application\Service\User\ProgramParticipationRepository,
    Domain\Model\User\ProgramParticipation\ParticipantComment
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

    public function execute(WorksheetCompositionId $worksheetCompositionId, string $commentId, string $message): string
    {
        $programParticipation = $this->programParticipationRepository->ofId(
                $worksheetCompositionId->getUserId(), $worksheetCompositionId->getProgramParticipationId());
        $id = $this->participantCommentRepository->nextIdentity();
        $comment = $this->commentRepoository->ofId($worksheetCompositionId, $commentId)->createReply($id, $message);
        $participantComment = new ParticipantComment($programParticipation, $id, $comment);
        $this->participantCommentRepository->add($participantComment);
        return $id;
    }

}
