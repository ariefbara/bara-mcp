<?php

namespace Query\Application\Service\Consultant;

use Query\Domain\Model\Firm\Program\DedicatedMentorRepository;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

class ViewDedicatedMentor
{

    /**
     * 
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    /**
     * 
     * @var DedicatedMentorRepository
     */
    protected $dedicatedMentorRepository;

    public function __construct(ConsultantRepository $consultantRepository,
            DedicatedMentorRepository $dedicatedMentorRepository)
    {
        $this->consultantRepository = $consultantRepository;
        $this->dedicatedMentorRepository = $dedicatedMentorRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $consultantId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $cancelledStatus
     * @return DedicatedMentor[]
     */
    public function showAll(
            string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        return $this->consultantRepository->aConsultantBelongsToPersonnel($firmId, $personnelId, $consultantId)
                        ->viewAllDedicatedMentors($this->dedicatedMentorRepository, $page, $pageSize, $cancelledStatus);
    }

    public function showById(string $firmId, string $personnelId, string $consultantId, string $dedicatedMentorId): DedicatedMentor
    {
        return $this->consultantRepository->aConsultantBelongsToPersonnel($firmId, $personnelId, $consultantId)
                        ->viewDedicatedMentor($this->$this->dedicatedMentorRepository, $dedicatedMentorId);
    }

}
