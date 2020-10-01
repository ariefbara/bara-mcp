<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultation;

use Query\{
    Application\Service\Firm\Personnel\ProgramConsultationRepository,
    Domain\Model\Firm\Program\Participant,
    Domain\Service\Firm\Program\ParticipantFinder
};

class ViewParticipant
{

    /**
     *
     * @var ProgramConsultationRepository
     */
    protected $programConsultationRepository;

    /**
     *
     * @var ParticipantFinder
     */
    protected $participantFinder;

    public function __construct(ProgramConsultationRepository $programConsultationRepository,
            ParticipantFinder $participantFinder)
    {
        $this->programConsultationRepository = $programConsultationRepository;
        $this->participantFinder = $participantFinder;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $programConsultationId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $activeStatus
     * @return Participant[]
     */
    public function showAll(
            string $firmId, string $personnelId, string $programConsultationId, int $page, int $pageSize,
            ?bool $activeStatus)
    {
        return $this->programConsultationRepository
                        ->aProgramConsultationOfPersonnel($firmId, $personnelId, $programConsultationId)
                        ->viewAllParticipant($this->participantFinder, $page, $pageSize, $activeStatus);
    }

    public function showById(string $firmId, string $personnelId, string $programConsultationId, string $participantId): Participant
    {
        return $this->programConsultationRepository
                        ->aProgramConsultationOfPersonnel($firmId, $personnelId, $programConsultationId)
                        ->viewParticipant($this->participantFinder, $participantId);
    }

}
