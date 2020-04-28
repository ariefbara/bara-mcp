<?php

namespace Client\Application\Service\Client;

class ProgramRegistrationCancel
{
    /**
     *
     * @var ProgramRegistrationRepository
     */
    protected $programRegistrationRepository;
    
    function __construct(ProgramRegistrationRepository $programRegistrationRepository)
    {
        $this->programRegistrationRepository = $programRegistrationRepository;
    }
    
    public function execute(string $clientId, string $programRegistrationId): void
    {
        $this->programRegistrationRepository->ofId($clientId, $programRegistrationId)
                ->cancel();
        $this->programRegistrationRepository->update();
    }

}
