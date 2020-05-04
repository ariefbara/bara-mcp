<?php

namespace Client\Application\Service\Client;

use Client\ {
    Application\Service\ClientRepository,
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Client\ProgramRegistration
};

class ProgramRegistrationSubmit
{

    /**
     *
     * @var ProgramRegistrationRepository
     */
    protected $programRegistrationRepository;

    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

    function __construct(ProgramRegistrationRepository $programRegistrationRepository,
            ClientRepository $clientRepository, ProgramRepository $programRepository)
    {
        $this->programRegistrationRepository = $programRegistrationRepository;
        $this->clientRepository = $clientRepository;
        $this->programRepository = $programRepository;
    }

    public function execute(string $clientId, string $firmId, string $programId): string
    {
        $id = $this->programRegistrationRepository->nextIdentity();
        $program = $this->programRepository->ofId($firmId, $programId);
        $programRegistration = $this->clientRepository->ofId($clientId)
                ->createProgramRegistration($id, $program);

        $this->programRegistrationRepository->add($programRegistration);
        return $id;
    }

}
