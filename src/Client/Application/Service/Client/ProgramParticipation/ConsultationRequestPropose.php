<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\{
    Application\Service\Client\ProgramParticipationRepository,
    Application\Service\Firm\Program\ConsultantRepository,
    Application\Service\Firm\Program\ConsultationSetupRepository,
    Domain\Model\Client\ProgramParticipation\ConsultationRequest
};
use DateTimeImmutable;
use Resources\Application\Event\Dispatcher;

class ConsultationRequestPropose
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    /**
     *
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            ConsultationRequestRepository $consultationRequestRepository,
            ProgramParticipationRepository $programParticipationRepository,
            ConsultationSetupRepository $consultationSetupRepository, ConsultantRepository $consultantRepository,
            Dispatcher $dispatcher)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->programParticipationRepository = $programParticipationRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->consultantRepository = $consultantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $clientId, string $programParticipationId, string $consultationSetupId, string $consultantId,
            DateTimeImmutable $startTime): ConsultationRequest
    {
        $consultationSetup = $this->consultationSetupRepository
                ->ofId($clientId, $programParticipationId, $consultationSetupId);
        $consultant = $this->consultantRepository->ofId($clientId, $programParticipationId, $consultantId);
        $programParticipation = $this->programParticipationRepository->ofId($clientId, $programParticipationId);
        $consultationRequest = $programParticipation
                ->createConsultationRequest($consultationSetup, $consultant, $startTime);
        $this->consultationRequestRepository->add($consultationRequest);

        $this->dispatcher->dispatch($programParticipation);
        return $consultationRequest;
    }

}
