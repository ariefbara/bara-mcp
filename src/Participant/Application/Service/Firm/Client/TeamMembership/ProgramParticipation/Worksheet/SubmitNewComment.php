<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet;

use Participant\Application\Service\Firm\Client\TeamMembershipRepository;
use Participant\Application\Service\Participant\Worksheet\CommentRepository;
use Participant\Application\Service\Participant\WorksheetRepository;

class SubmitNewComment
{

    /**
     *
     * @var MemberCommentRepository
     */
    protected $memberCommentRepository;

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

    public function __construct(MemberCommentRepository $memberCommentRepository,
            WorksheetRepository $worksheetRepository, TeamMembershipRepository $teamMembershipRepository)
    {
        $this->memberCommentRepository = $memberCommentRepository;
        $this->worksheetRepository = $worksheetRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $worksheetId, string $message): string
    {
        $worksheet = $this->worksheetRepository
                ->aWorksheetBelongsToTeamParticipant($teamProgramParticipationId, $worksheetId);
        $id = $this->memberCommentRepository->nextIdentity();

        $comment = $this->teamMembershipRepository
                ->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamMembershipId)
                ->submitNewCommentInWorksheet($worksheet, $id, $message);
        $this->memberCommentRepository->add($comment);

        return $id;
    }

}
