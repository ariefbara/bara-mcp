<?php

namespace Query\Application\Service\Firm\Program\Participant\Worksheet;

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
     * @param string $programId
     * @param string $participantId
     * @param string $worksheetId
     * @param int $page
     * @param int $pageSize
     * @return Comment[]
     */
    public function showAll(string $firmId, string $programId, string $participantId, string $worksheetId, int $page,
            int $pageSize)
    {
        return $this->commentRepository->all($firmId, $programId, $participantId, $worksheetId, $page, $pageSize);
    }

    public function showById(string $firmId, string $programId, string $participantId, string $worksheetId,
            string $commentId): Comment
    {
        return $this->commentRepository->ofId($firmId, $programId, $participantId, $worksheetId, $commentId);
    }

}
