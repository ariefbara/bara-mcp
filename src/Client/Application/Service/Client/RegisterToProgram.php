<?php

namespace Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;
use Resources\Application\Event\Dispatcher;

class RegisterToProgram
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

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(
            ProgramRegistrationRepository $programRegistrationRepository, ClientRepository $clientRepository,
            ProgramRepository $programRepository, Dispatcher $dispatcher)
    {
        $this->programRegistrationRepository = $programRegistrationRepository;
        $this->clientRepository = $clientRepository;
        $this->programRepository = $programRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $clientId, string $programId): string
    {
        $id = $this->programRegistrationRepository->nextIdentity();
        $program = $this->programRepository->ofId($firmId, $programId);
        
        $client = $this->clientRepository->ofId($firmId, $clientId);
        $programRegistration = $client->registerToProgram($id, $program);
        
        $this->programRegistrationRepository->add($programRegistration);
        
        $this->dispatcher->dispatch($client);
        
        return $id;
    }
    
}
