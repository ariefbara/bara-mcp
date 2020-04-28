<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\ConsultationSetup;

class ConsultationSetupView
{
    /**
     *
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;
    
    function __construct(ConsultationSetupRepository $consultationSetupRepository)
    {
        $this->consultationSetupRepository = $consultationSetupRepository;
    }
    
    public function showById(ProgramCompositionId $programCompositionId, string $consultationSetupId): ConsultationSetup
    {
        return $this->consultationSetupRepository->ofId($programCompositionId, $consultationSetupId);
    }
    
    /**
     * 
     * @param ProgramCompositionId $programCompositionId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationSetup[]
     */
    public function showAll(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        return $this->consultationSetupRepository->all($programCompositionId, $page, $pageSize);
    }

}
