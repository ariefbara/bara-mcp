<?php

namespace Client\Application\Service\Client;

class CancelProgramRegistration
{
    /**
     *
     * @var ProgramRegistrationRepository
     */
    protected $programRegistrationRepository;
    
    public function __construct(ProgramRegistrationRepository $programRegistrationRepository)
    {
        $this->programRegistrationRepository = $programRegistrationRepository;
    }
    
    public function execute(string $firmid, string $clientId, string $programRegistrationId): void
    {
        $this->programRegistrationRepository->ofId($firmid, $clientId, $programRegistrationId)->cancel();
        $this->programRegistrationRepository->update();
    }

}
