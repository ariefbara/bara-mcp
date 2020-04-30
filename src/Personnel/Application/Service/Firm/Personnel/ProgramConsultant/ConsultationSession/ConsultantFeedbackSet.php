<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\{
    ConsultationSessionRepository,
    ProgramConsultantCompositionId
};
use Shared\Domain\Model\FormRecordData;

class ConsultantFeedbackSet
{

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    function __construct(ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    public function execute(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationSessionId,
            FormRecordData $formRecordData): void
    {
        $this->consultationSessionRepository->ofId($programConsultantCompositionId, $consultationSessionId)
                ->setConsultantFeedback($formRecordData);
        $this->consultationSessionRepository->update();
    }

}
