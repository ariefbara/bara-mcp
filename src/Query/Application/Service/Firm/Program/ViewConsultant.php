<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Consultant;

class ViewConsultant
{
    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;
    
    public function __construct(ConsultantRepository $consultantRepository)
    {
        $this->consultantRepository = $consultantRepository;
    }
    
    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return Consultant[]
     */
    public function showAll(string $firmId, string $programId, int $page, int $pageSize)
    {
        return $this->consultantRepository->all($firmId, $programId, $page, $pageSize);
    }
    
    public function showById(string $firmId, string $programId, string $consultantId): Consultant
    {
        return $this->consultantRepository->ofId($firmId, $programId, $consultantId);
    }

}
