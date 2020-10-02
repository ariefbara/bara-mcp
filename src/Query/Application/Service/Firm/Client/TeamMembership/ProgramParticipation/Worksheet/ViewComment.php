<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

class ViewComment
{

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;
    
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamMembershipId
     * @param string $teamProgramParticipationId
     * @param string $worksheetId
     * @param int $page
     * @param int $pageSize
     * @return Comment[]
     */
    public function showAll(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $worksheetId, int $page, int $pageSize)
    {
        return $this->commentRepository->allCommentsBelongsToTeamWhereClientIsMember(
                        $firmId, $clientId, $teamMembershipId, $teamProgramParticipationId, $worksheetId, $page,
                        $pageSize);
    }

    public function showById(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $worksheetId, string $commentId): Comment
    {
        return $this->commentRepository->aCommentBelongsToTeamWhereClientIsMember(
                        $firmId, $clientId, $teamMembershipId, $teamProgramParticipationId, $worksheetId, $commentId);
    }

}
