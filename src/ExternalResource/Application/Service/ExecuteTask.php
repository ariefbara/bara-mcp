<?php

namespace ExternalResource\Application\Service;

use ExternalResource\Domain\Model\ExternalEntity;
use ExternalResource\Domain\Model\ExternalTask;

class ExecuteTask
{

    /**
     * 
     * @var ExternalEntity
     */
    protected $externalEntity;

    public function __construct(ExternalEntity $externalEntity)
    {
        $this->externalEntity = $externalEntity;
    }
    
    public function execute(ExternalTask $task, $payload): void
    {
        $this->externalEntity->executeExternalTask($task, $payload);
    }

}
