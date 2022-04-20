<?php

namespace Firm\Application\Service;

use Firm\Domain\Model\ResponsiveTask;

class ExecuteResponsiveTask
{
    /**
     * 
     * @var GenericRepository
     */
    protected $genericRepository;
    
    public function __construct(GenericRepository $genericRepository)
    {
        $this->genericRepository = $genericRepository;
    }
    
    public function execute(ResponsiveTask $task, $payload): void
    {
        $task->execute($payload);
        $this->genericRepository->update();
    }

    
    
}
