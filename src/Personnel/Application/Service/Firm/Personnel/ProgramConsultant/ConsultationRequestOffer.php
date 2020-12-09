<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultantRepository;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequestData;
use Resources\Application\Event\Dispatcher;

class ConsultationRequestOffer
{

    /**
     *
     * @var ProgramConsultantRepository
     */
    protected $programConsultantRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(ProgramConsultantRepository $programConsultantRepository, Dispatcher $dispatcher)
    {
        $this->programConsultantRepository = $programConsultantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $personnelId, string $programConsultantId,
            string $consultationRequestId, ConsultationRequestData $consultationRequestData): void
    {
        $programConsultant = $this->programConsultantRepository->ofId($firmId, $personnelId, $programConsultantId);
        $programConsultant->offerConsultationRequestTime($consultationRequestId, $consultationRequestData);
        $this->programConsultantRepository->update();
        
        $this->dispatcher->dispatch($programConsultant);
    }

}
