<?php

namespace Query\Application\Service\Firm\Personnel;

use Query\Domain\Model\Firm\Program\Consultant;

class ViewProgramConsultation
{
    /**
     *
     * @var ProgramConsultationRepository
     */
    protected $programConsultationRepository;
    
    public function __construct(ProgramConsultationRepository $programConsultationRepository)
    {
        $this->programConsultationRepository = $programConsultationRepository;
    }
    
    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param int $page
     * @param int $pageSize
     * @return Consultant[]
     */
    public function showAll(string $firmId, string $personnelId, int $page, int $pageSize)
    {
        return $this->programConsultationRepository->allProgramConsultationOfPersonnel($firmId, $personnelId, $page, $pageSize);
    }
    
    public function showById(string $firmId, string $personnelId, string $programConsultationId): Consultant
    {
        return $this->programConsultationRepository->aProgramConsultationOfPersonnel($firmId, $personnelId, $programConsultationId);
    }

}
