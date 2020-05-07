<?php

namespace Query\Application\Service\Firm\Program\Participant\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;


class CommentView
{

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function showById(WorksheetCompositionId $worksheetCompositionId, string $commentId): Comment
    {
        return $this->commentRepository->ofId($worksheetCompositionId, $commentId);
    }

    /**
     * 
     * @param WorksheetCompositionId $worksheetCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Comment[]
     */
    public function showAll(WorksheetCompositionId $worksheetCompositionId, int $page, int $pageSize)
    {
        return $this->commentRepository->all($worksheetCompositionId, $page, $pageSize);
    }

}
