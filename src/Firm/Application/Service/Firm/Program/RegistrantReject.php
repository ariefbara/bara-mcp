<?php

namespace Firm\Application\Service\Firm\Program;

class RegistrantReject
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

    public function execute(ProgramCompositionId $programCompositionId, string $registrantId): void
    {
        $this->registrantRepository->ofId($programCompositionId, $registrantId)
            ->reject();
        $this->registrantRepository->update();
    }

}
