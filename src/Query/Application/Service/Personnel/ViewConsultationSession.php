<?php

namespace Query\Application\Service\Personnel;

use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

class ViewConsultationSession
{

    /**
     * 
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     * 
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    public function __construct(PersonnelRepository $personnelRepository,
            ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->personnelRepository = $personnelRepository;
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationSessionFilter|null $consultationSessionFilter
     * @return ConsultationSession[]
     */
    public function showAll(
            string $firmId, string $personnelId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->personnelRepository->aPersonnelInFirm($firmId, $personnelId)
                        ->viewAllConsultationSessions(
                                $this->consultationSessionRepository, $page, $pageSize, $consultationSessionFilter);
    }

}
