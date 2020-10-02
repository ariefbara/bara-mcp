<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet;

use Participant\Application\Service\{
    Firm\Client\TeamMembershipRepository,
    Participant\Worksheet\CommentRepository,
    Participant\WorksheetRepository
};

class SubmitNewComment
{

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    public function __construct(CommentRepository $commentRepository, WorksheetRepository $worksheetRepository,
            TeamMembershipRepository $teamMembershipRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->worksheetRepository = $worksheetRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $worksheetId, string $message): string
    {
        $worksheet = $this->worksheetRepository
                ->aWorksheetBelongsToTeamParticipant($teamProgramParticipationId, $worksheetId);
        $id = $this->commentRepository->nextIdentity();

        $comment = $this->teamMembershipRepository
                ->ofId($firmId, $clientId, $teamMembershipId)
                ->submitNewCommentInWorksheet($worksheet, $id, $message);
        $this->commentRepository->add($comment);

        return $id;
    }

}
