<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\ProgramRepository;
use Resources\Application\Event\Dispatcher;

class AcceptClientRegistration
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

    function __construct(ProgramRepository $programRepository, Dispatcher $dispatcher)
    {
        $this->programRepository = $programRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $programId, string $clientRegistrantId): void
    {
        $program = $this->programRepository->ofId($firmId, $programId);
        $program->acceptClientRegistration($clientRegistrantId);
        $this->programRepository->update();
        
        $this->dispatcher->dispatch($program);
    }

}
