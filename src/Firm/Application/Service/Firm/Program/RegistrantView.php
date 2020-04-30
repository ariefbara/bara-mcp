<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Registrant;

class RegistrantView
{
    /**
     *
     * @var RegistrantRepository
     */
    protected $registrantRepository;
    
    function __construct(RegistrantRepository $registrantRepository)
    {
        $this->registrantRepository = $registrantRepository;
    }
    
    public function showById(ProgramCompositionId $programCompositionId, string $registrantId): Registrant
    {
        return $this->registrantRepository->ofId($programCompositionId, $registrantId);
    }
    
    /**
     * 
     * @param ProgramCompositionId $programCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Registrant[]
     */
    public function showAll(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        return $this->registrantRepository->all($programCompositionId, $page, $pageSize);
    }

}
