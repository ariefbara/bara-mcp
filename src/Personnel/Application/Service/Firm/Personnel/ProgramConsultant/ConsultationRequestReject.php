<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Resources\Application\Event\Dispatcher;

class ConsultationRequestReject
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(ConsultationRequestRepository $consultationRequestRepository, Dispatcher $dispatcher)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $personnelId, string $programConsultantId, string $consultationRequestId): void
    {
        $consultationReqest = $this->consultationRequestRepository
                ->ofId($firmId, $personnelId, $programConsultantId, $consultationRequestId);
        $consultationReqest->reject();
        $this->consultationRequestRepository->update();

        $this->dispatcher->dispatch($consultationReqest);
    }

}
