<?php

namespace Participant\Application\Service\UserParticipant\Worksheet;

use Participant\Application\Service\Participant\Worksheet\CommentRepository;

class RemoveComment
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

    public function execute(string $userId, string $userParticipantId, string $worksheetId, string $commentId): void
    {
        $this->commentRepository
                ->aCommentInUserParticipantWorksheet($userId, $userParticipantId, $worksheetId, $commentId)
                ->remove($teamMember = null);
        $this->commentRepository->update();
    }
}
