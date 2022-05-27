<?php

namespace Firm\Domain\Task\Responsive;

use Firm\Domain\Model\ResponsiveTask;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;
use Resources\Application\Event\AdvanceDispatcher;

class ReceiveProgramApplicationFromClient implements ResponsiveTask
{

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(ProgramRepository $programRepository, ClientRepository $clientRepository,
            AdvanceDispatcher $dispatcher)
    {
        $this->programRepository = $programRepository;
        $this->clientRepository = $clientRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param ReceiveProgramApplicationPayload $payload
     * @return void
     */
    public function execute($payload): void
    {
        $program = $this->programRepository->aProgramOfId($payload->getProgramId());
        $client = $this->clientRepository->ofId($payload->getApplicantId());
        $program->receiveApplication($client);
        $this->dispatcher->dispatch($program);
    }
    
}
