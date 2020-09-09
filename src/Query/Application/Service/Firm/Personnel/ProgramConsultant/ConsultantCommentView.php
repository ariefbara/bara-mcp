<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\Domain\Model\Firm\Program\Consultant\ConsultantComment;

class ConsultantCommentView
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
    
    public function showById(string $firmId, string $personnelId, string $programConsultationId, string $consultantCommentId): ConsultantComment
    {
        return $this->consultantCommentRepository->ofId($firmId, $personnelId, $programConsultationId, $consultantCommentId);
    }

}
