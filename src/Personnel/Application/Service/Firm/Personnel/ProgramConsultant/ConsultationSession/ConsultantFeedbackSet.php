<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

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
            string $firmId, string $personnelId, string $programConsultantId, string $consultationSessionId,
            FormRecordData $formRecordData): void
    {
        $this->consultationSessionRepository->ofId($firmId, $personnelId, $programConsultantId, $consultationSessionId)
                ->setConsultantFeedback($formRecordData);
        $this->consultationSessionRepository->update();
    }

}
