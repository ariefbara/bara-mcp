<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation\Worksheet;

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
     * @param string $teamId
     * @param string $worksheetId
     * @param int $page
     * @param int $pageSize
     * @return Comment[]
     */
    public function showAll(string $teamId, string $worksheetId, int $page, int $pageSize)
    {
        return $this->commentRepository->allCommentsInWorksheetBelongsToTeam($teamId, $worksheetId, $page, $pageSize);
    }

    public function showById(string $teamId, string $commentId): Comment
    {
        return $this->commentRepository->aCommentBelongsToTeam($teamId, $commentId);
    }

}
