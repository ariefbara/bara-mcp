<?php

namespace Participant\Application\Service\Participant;

use DateTimeImmutable;
use Participant\Application\Service\ClientParticipantRepository;
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
            string $firmId, string $clientId, string $programId, string $consultationSetupId, string $personnelId,
            DateTimeImmutable $startTime): string
    {
        $id = $this->consultationRequestRepository->nextIdentity();
        $consultationSetup = $this->consultationSetupRepository->ofId($firmId, $programId, $consultationSetupId);
        $consultant = $this->consultantRepository->ofId($firmId, $programId, $personnelId);
        
        $clientParticipant = $this->clientParticipantRepository->ofId($firmId, $clientId, $programId);
        $consulatationRequest = $clientParticipant->proposeConsultation($id, $consultationSetup, $consultant, $startTime);
        $this->consultationRequestRepository->add($consulatationRequest);
        
        $this->dispatcher->dispatch($clientParticipant);
        
        return $id;
    }

    /*
      public function execute(
      string $userId, string $programParticipationId, string $consultationSetupId, string $consultantId,
      DateTimeImmutable $startTime): string
      {
      $id = $this->consultationRequestRepository->nextIdentity();
      $consultationSetup = $this->consultationSetupRepository
      ->aConsultationSetupInProgramWhereUserParticipate($userId, $programParticipationId, $consultationSetupId);
      $consultant = $this->consultantRepository->aConsultantInProgramWhereUserParticipate($userId, $programParticipationId, $consultantId);
      $programParticipation = $this->programParticipationRepository->ofId($userId, $programParticipationId);
      $consultationRequest = $programParticipation
      ->createConsultationRequest($id, $consultationSetup, $consultant, $startTime);
      $this->consultationRequestRepository->add($consultationRequest);

      $this->dispatcher->dispatch($programParticipation);
      return $id;
      }
     * 
     */
}
