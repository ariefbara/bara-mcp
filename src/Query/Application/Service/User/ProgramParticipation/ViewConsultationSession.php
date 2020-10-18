<?php

namespace Query\Application\Service\User\ProgramParticipation;

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
     * @param string $userId
     * @param string $userParticipantId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationSession[]
     */
    public function showAll(
            string $userId, string $userParticipantId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->consultationSessionRepository->allConsultationSessionFromUserParticipant(
                        $userId, $userParticipantId, $page, $pageSize, $consultationSessionFilter);
    }

    public function showById(string $userId, string $userParticipantId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->aConsultationSessionFromUserParticipant($userId,
                        $userParticipantId, $consultationSessionId);
    }

}
