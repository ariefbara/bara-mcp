<?php

namespace Query\Application\Service\User\ProgramParticipation\Worksheet;

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
     * @param string $userId
     * @param string $userParticipantId
     * @param string $worksheetId
     * @param int $page
     * @param int $pageSize
     * @return Comment[]
     */
    public function showAll(
            string $userId, string $userParticipantId, string $worksheetId, int $page, int $pageSize)
    {
        return $this->commentRepository->allCommentInUserParticipantWorksheet(
                        $userId, $userParticipantId, $worksheetId, $page, $pageSize);
    }

    public function showById(string $userId, string $userParticipantId, string $worksheetId, string $commentId): Comment
    {
        return $this->commentRepository->aCommentInUserParticipantWorksheet(
                        $userId, $userParticipantId, $worksheetId, $commentId);
    }

}
