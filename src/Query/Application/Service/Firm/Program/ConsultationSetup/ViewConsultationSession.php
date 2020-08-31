<?php

namespace Query\Application\Service\Firm\Program\ConsulationSetup;

use Query\Domain\Model\Firm\Program\Participant\ConsultationSession;

class ViewConsultationSession
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

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param string $consultationSetupId
     * @param ConsultationSessionFilter|null $consultationSessionFilter
     * @return ConsultationSession[]
     */
    public function showAll(
            string $firmId, string $programId, string $consultationSetupId,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->consultationSessionRepository->all($firmId, $programId, $consultationSetupId,
                        $consultationSessionFilter);
    }

    public function showById(
            string $firmId, string $programId, string $consultationSetupId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->ofId(
                        $firmId, $programId, $consultationSetupId, $consultationSessionId);
    }

}
