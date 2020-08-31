<?php

namespace User\Application\Service\User\ProgramParticipation;

use User\{
    Application\Service\User\ProgramParticipationRepository,
    Application\Service\Firm\Program\ConsultantRepository,
    Application\Service\Firm\Program\ConsultationSetupRepository,
    Domain\Model\User\ProgramParticipation\ConsultationRequest
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

}
