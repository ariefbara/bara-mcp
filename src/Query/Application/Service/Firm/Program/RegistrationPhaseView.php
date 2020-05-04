<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\RegistrationPhase;

class RegistrationPhaseView
{

    /**
     *
     * @var RegistrationPhaseRepository
     */
    protected $registrationPhaseRepository;

    function __construct(RegistrationPhaseRepository $registrationPhaseRepository)
    {
        $this->registrationPhaseRepository = $registrationPhaseRepository;
    }

    public function showById(ProgramCompositionId $programCompositionId, string $registrationPhaseId): RegistrationPhase
    {
        return $this->registrationPhaseRepository->ofId($programCompositionId, $registrationPhaseId);
    }

    /**
     * 
     * @param ProgramCompositionId $programCompositionId
     * @param int $page
     * @param int $pageSize
     * @return RegistrationPhase[]
     */
    public function showAll(ProgramCompositionId $programCompositionId, int $page, int$pageSize)
    {
        return $this->registrationPhaseRepository->all($programCompositionId, $page, $pageSize);
    }

}
