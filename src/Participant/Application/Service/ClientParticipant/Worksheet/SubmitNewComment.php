<?php

namespace Participant\Application\Service\ClientParticipant\Worksheet;

use Participant\Application\Service\Participant\ {
    Worksheet\CommentRepository,
    WorksheetRepository
};


class SubmitNewComment
{

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    public function __construct(CommentRepository $commentRepository,
            WorksheetRepository $worksheetRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->worksheetRepository = $worksheetRepository;
    }

    public function execute(string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $message): string
    {
        $id = $this->commentRepository->nextIdentity();
        $comment = $this->worksheetRepository
                ->aWorksheetBelongsToClientParticipant($firmId, $clientId, $programParticipationId, $worksheetId)
                ->createComment($id, $message);

        $this->commentRepository->add($comment);
        return $id;
    }

}
