<?php

namespace Participant\Application\Service\ClientParticipant\Worksheet;

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

    public function execute(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $commentId): void
    {
        $this->commentRepository
                ->aCommentInClientParticipantWorksheet(
                        $firmId, $clientId, $programParticipationId, $worksheetId, $commentId)
                ->remove($teamMember = null);
        $this->commentRepository->update();
    }

}
