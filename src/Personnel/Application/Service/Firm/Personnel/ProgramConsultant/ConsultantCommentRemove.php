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
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultantCommentId): void
    {
        $this->consultantCommentRepository->ofId($programConsultantCompositionId, $consultantCommentId)
                ->remove();
        $this->consultantCommentRepository->update();
    }

}
