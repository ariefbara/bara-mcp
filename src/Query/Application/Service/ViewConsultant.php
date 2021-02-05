<?php

namespace Query\Application\Service;

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
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return Consultant[]
     */
    public function showAll(string $programId, int $page, int $pageSize)
    {
        return $this->consultantRepository->allActiveConsultantInProgram($programId, $page, $pageSize);
    }
    
    public function showById(string $id): Consultant
    {
        return $this->consultantRepository->anActiveConsultant($id);
    }

}
