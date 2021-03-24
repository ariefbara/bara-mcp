<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\DedicatedMentorRepository;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

class ViewDedicatedMentor
{

    /**
     * 
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     * 
     * @var DedicatedMentorRepository
     */
    protected $dedicatedMentorRepository;

    public function __construct(CoordinatorRepository $coordinatorRepository,
            DedicatedMentorRepository $dedicatedMentorRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
        $this->dedicatedMentorRepository = $dedicatedMentorRepository;
    }

    public function showById(string $firmId, string $personnelId, string $programId, string $dedicatedMentorId): DedicatedMentor
    {
        return $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                        ->viewDedicatedMentor($this->dedicatedMentorRepository, $dedicatedMentorId);
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $programId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $cancelledStatus
     * @return DedicatedMentor[]
     */
    public function showAllBelongsToParticipant(
            string $firmId, string $personnelId, string $programId, string $participantId, int $page, int $pageSize,
            ?bool $cancelledStatus)
    {
        return $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                        ->viewAllDedicatedMentorOfParticipant(
                                $this->dedicatedMentorRepository, $participantId, $page, $pageSize, $cancelledStatus);
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $programId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $cancelledStatus
     * @return DedicatedMentor[]
     */
    public function showAllBelongsToConsultant(
            string $firmId, string $personnelId, string $programId, string $participantId, int $page, int $pageSize,
            ?bool $cancelledStatus)
    {
        return $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                        ->viewAllDedicatedMentorOfConsultant(
                                $this->dedicatedMentorRepository, $participantId, $page, $pageSize, $cancelledStatus);
    }

}
