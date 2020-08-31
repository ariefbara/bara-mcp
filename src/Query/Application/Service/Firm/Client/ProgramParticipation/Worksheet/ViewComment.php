<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation\Worksheet;

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
     * @param string $programId
     * @param string $worksheetId
     * @param int $page
     * @param int $pageSize
     * @return Comment[]
     */
    public function showAll(
            string $firmId, string $clientId, string $programId, string $worksheetId, int $page, int $pageSize)
    {
        return $this->commentRepository
                        ->allCommentsInClientWorksheet($firmId, $clientId, $programId, $worksheetId, $page, $pageSize);
    }

    public function showById(
            string $firmId, string $clientId, string $programId, string $worksheetId, string $commentId): Comment
    {
        return $this->commentRepository
                        ->aCommentInClientWorksheet($firmId, $clientId, $programId, $worksheetId, $commentId);
    }

}
