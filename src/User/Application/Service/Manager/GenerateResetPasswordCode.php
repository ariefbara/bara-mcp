<?php

namespace User\Application\Service\Manager;

use Resources\Application\Event\Dispatcher;

class GenerateResetPasswordCode
{

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(ManagerRepository $managerRepository, Dispatcher $dispatcher)
    {
        $this->managerRepository = $managerRepository;
        $this->dispatcher = $dispatcher;
    }
    
    public function execute(string $firmIdentifier, string $email): void
    {
        $manager = $this->managerRepository->aManagerInFirmByEmailAndIdentifier($firmIdentifier, $email);
        $manager->generateResetPasswordCode();
        $this->managerRepository->update();
        
        $this->dispatcher->dispatch($manager);
    }

}
