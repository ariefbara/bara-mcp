<?php

namespace Participant\Application\Service\Participant\Worksheet;

use Participant\Application\Service\Participant\WorksheetRepository;

class SubmitNewComment
{

    /**
     *
     * @var ParticipantCommentRepository
     */
    protected $participantCommentRepository;

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    public function __construct(ParticipantCommentRepository $participantCommentRepository,
            WorksheetRepository $worksheetRepository)
    {
        $this->participantCommentRepository = $participantCommentRepository;
        $this->worksheetRepository = $worksheetRepository;
    }

    public function execute(string $firmId, string $clientId, string $programId, string $worksheetId, string $message): string
    {
        $id = $this->participantCommentRepository->nextIdentity();
        $participantComment = $this->worksheetRepository
                ->aWorksheetOfClientParticipant($firmId, $clientId, $programId, $worksheetId)
                ->createComment($id, $message);

        $this->participantCommentRepository->add($participantComment);
        return $id;
    }

}
