<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\ProgramRepository;
use Resources\Application\Event\Dispatcher;

class AcceptUserRegistration
{

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

    public function __construct(ProgramRepository $programRepository, Dispatcher $dispatcher)
    {
        $this->programRepository = $programRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $programId, string $userRegistrantId): void
    {
        $program = $this->programRepository->ofId($firmId, $programId);
        $program->acceptUserRegistration($userRegistrantId);
        $this->programRepository->update();
        
        $this->dispatcher->dispatch($program);
    }

}
