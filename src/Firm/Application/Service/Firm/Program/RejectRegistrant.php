<?php

namespace Firm\Application\Service\Firm\Program;

class RejectRegistrant
{
    /**
     *
     * @var RegistrantRepository
     */
    protected $registrantRepository;
    
    public function __construct(RegistrantRepository $registrantRepository)
    {
        $this->registrantRepository = $registrantRepository;
    }
    
    public function execute(string $firmId, string $programId, string $registrantId): void
    {
        $this->registrantRepository->ofId($firmId, $programId, $registrantId)->reject();
        $this->registrantRepository->update();
    }

}
