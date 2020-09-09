<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultantRepository;
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
            string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $programConsultant = $this->programConsultantRepository->ofId($firmId, $personnelId, $programConsultantId);
        $programConsultant->offerConsultationRequestTime($consultationRequestId, $startTime);
        $this->programConsultantRepository->update();
        
        $this->dispatcher->dispatch($programConsultant);
    }

}
