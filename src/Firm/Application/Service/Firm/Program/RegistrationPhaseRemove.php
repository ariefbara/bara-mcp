<?php

namespace Firm\Application\Service\Firm\Program;

class RegistrationPhaseRemove
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

    public function execute(
            ProgramCompositionId $programCompositionId, string $registrationPhaseId): void
    {
        $this->registrationPhaseRepository->ofId($programCompositionId, $registrationPhaseId)
                ->remove();
        $this->registrationPhaseRepository->update();
    }

}
