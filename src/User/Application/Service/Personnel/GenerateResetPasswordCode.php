<?php

namespace User\Application\Service\Personnel;

use Resources\Application\Event\Dispatcher;

class GenerateResetPasswordCode
{

    /**
     *
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;
    
    function __construct(PersonnelRepository $personnelRepository, Dispatcher $dispatcher)
    {
        $this->personnelRepository = $personnelRepository;
        $this->dispatcher = $dispatcher;
    }
    
    public function execute(string $firmIdentifier, string $email): void
    {
        $personnel = $this->personnelRepository->aPersonnelInFirmByEmailAndIdentifier($firmIdentifier, $email);
        $personnel->generateResetPasswordCode();
        $this->personnelRepository->update();
        
        $this->dispatcher->dispatch($personnel);
    }


}
