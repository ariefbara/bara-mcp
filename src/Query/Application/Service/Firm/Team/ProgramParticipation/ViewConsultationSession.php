<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\ {
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

class ViewConsultationSession
{
    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;
    
    public function __construct(ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
    }
    
    /**
     * 
     * @param string $teamId
     * @param string $teamProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationSessionFilter|null $consultationSessionFilter
     * @return ConsultationSession[]
     */
    public function showAll(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->consultationSessionRepository->allConsultationSessionsInProgramParticipationOfTeam(
                $teamId, $teamProgramParticipationId, $page, $pageSize, $consultationSessionFilter);
    }
    
    public function showById(string $teamId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->aConsultationSessionBelongsToTeam($teamId, $consultationSessionId);
    }

}
