<?php

namespace Query\Application\Service\Firm\Program\ClientParticipant\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\ConsultantComment;

class ViewConsultantComment
{
    /**
     *
     * @var ConsultantCommentRepository
     */
    protected $consultantCommentRepository;
    
    public function __construct(ConsultantCommentRepository $consultantCommentRepository)
    {
        $this->consultantCommentRepository = $consultantCommentRepository;
    }
    
    public function showById(string $firmId, string $programId, string $clientId, string $worksheetId, string $consultantCommentId): ConsultantComment
    {
        return $this->consultantCommentRepository->aConsultantCommentOfClientParticipant($firmId, $programId, $clientId, $worksheetId, $consultantCommentId);
    }

}
