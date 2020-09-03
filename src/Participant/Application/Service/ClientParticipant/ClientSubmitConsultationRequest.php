<?php

namespace Participant\Application\Service\ClientParticipant;

use DateTimeImmutable;
use Participant\Application\Service\{
    ClientParticipantRepository,
    Participant\ConsultationRequestRepository
};
use Resources\Application\Event\Dispatcher;

class ClientSubmitConsultationRequest
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    /**
     *
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

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

    public function __construct(ConsultationRequestRepository $consultationRequestRepository,
            ClientParticipantRepository $clientParticipantRepository,
            ConsultationSetupRepository $consultationSetupRepository, ConsultantRepository $consultantRepository,
            Dispatcher $dispatcher)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->consultantRepository = $consultantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $programParticipationId, string $consultationSetupId,
            string $consultantId, DateTimeImmutable $startTime): string
    {
        $id = $this->consultationRequestRepository->nextIdentity();
        $consultationSetup = $this->consultationSetupRepository
                ->aConsultationSetupInProgramWhereClientParticipate(
                        $firmId, $clientId, $programParticipationId, $consultationSetupId);
        $consultant = $this->consultantRepository
                ->aConsultantInProgramWhereClientParticipate($firmId, $clientId, $programParticipationId, $consultantId);

        $clientParticipant = $this->clientParticipantRepository->ofId($firmId, $clientId, $programParticipationId);
        $consulatationRequest = $clientParticipant->proposeConsultation($id, $consultationSetup, $consultant, $startTime);
        $this->consultationRequestRepository->add($consulatationRequest);

        $this->dispatcher->dispatch($clientParticipant);

        return $id;
    }

}
