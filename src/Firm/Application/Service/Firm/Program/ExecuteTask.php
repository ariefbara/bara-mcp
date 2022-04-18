<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\ProgramRepository;
use Firm\Domain\Model\Firm\IProgramTask;

class ExecuteTask
{
    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;
    
    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }
    
    public function execute(string $programId, IProgramTask $task, $payload): void
    {
        $this->programRepository->aProgramOfId($programId)->executeTask($task, $payload);
        $this->programRepository->update();
    }

}
