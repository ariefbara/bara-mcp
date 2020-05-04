<?php

namespace Query\Application\Service\Client\ProgramParticipation\Worksheet;

use Client\Application\Service\Client\ProgramParticipation\Worksheet\WorksheetCompositionId;
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
    
    /**
     * 
     * @param WorksheetCompositionId $worksheetCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Comment[]
     */
    public function showAll(WorksheetCompositionId $worksheetCompositionId, int $page, int $pageSize)
    {
        return $this->commentRepository->allCommentsInWorksheetOfParticipant($worksheetCompositionId, $page, $pageSize);
    }
    
    public function showById(WorksheetCompositionId $worksheetCompositionId, string $commentId): Comment
    {
        return $this->commentRepository->aCommentInWorksheetOfParticipant($worksheetCompositionId, $commentId);
    }

}
