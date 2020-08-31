<?php

namespace User\Application\Service\User;

class CancelProgramRegistration
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
    
    public function execute(string $userId, string $programRegistrationId): void
    {
        $this->programRegistrationRepository->ofId($userId, $programRegistrationId)
                ->cancel();
        $this->programRegistrationRepository->update();
    }

}
