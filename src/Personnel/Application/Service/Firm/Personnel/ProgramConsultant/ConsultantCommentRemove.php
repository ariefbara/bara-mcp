<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

class ConsultantCommentRemove
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

    public function execute(
            string $firmId, string $personnelId, string $programConsultationId, string $consultantCommentId): void
    {
        $this->consultantCommentRepository->ofId($firmId, $personnelId, $programConsultationId, $consultantCommentId)
                ->remove();
        $this->consultantCommentRepository->update();
    }

}
