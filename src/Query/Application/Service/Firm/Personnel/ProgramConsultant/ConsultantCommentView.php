<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantComment;

class ConsultantCommentView
{

    /**
     *
     * @var ConsultantCommentRepository
     */
    protected $consultantCommentRepository;

    function __construct(ConsultantCommentRepository $consultantCommentRepository)
    {
        $this->consultantCommentRepository = $consultantCommentRepository;
    }

    public function showById(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultantCommentId): ConsultantComment
    {
        return $this->consultantCommentRepository->aCommentFromProgramConsultant(
                        $programConsultantCompositionId, $consultantCommentId);
    }

}
