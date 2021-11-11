<?php

namespace Query\Application\Service\Personnel;

use Query\Domain\Model\Firm\Personnel;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Tests\TestBase;

class ExecuteQueryTaskTest extends TestBase
{
    protected $personnelRepository;
    protected $personnel;
    protected $personnelId = 'personnelId', $firmId = 'firmId';
    protected $service;
    
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnelRepository->expects($this->any())
                ->method('aPersonnelInFirm')
                ->with($this->firmId, $this->personnelId)
                ->willReturn($this->personnel);
        
        $this->service = new ExecuteQueryTask($this->personnelRepository);
        
        $this->task = $this->buildMockOfInterface(TaskExecutableByPersonnel::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->task);
    }
    public function test_execute()
    {
        $this->personnel->expects($this->once())
                ->method('executeTask')
                ->with($this->task);
        $this->execute();
    }
}
