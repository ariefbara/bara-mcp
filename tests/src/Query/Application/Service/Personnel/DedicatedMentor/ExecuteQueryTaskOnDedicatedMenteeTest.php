<?php

namespace Query\Application\Service\Personnel\DedicatedMentor;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\QueryTaskOnDedicatedMenteeExecutableByDedicatedMentor;
use Tests\TestBase;

class ExecuteQueryTaskOnDedicatedMenteeTest extends TestBase
{
    protected $dedicatedMentorRepository;
    protected $dedicatedMentor;
    protected $personnelId = 'personnelId', $dedicatedMentorId = 'dedicatedMentorId';
    protected $service;
    //
    protected $task;
    protected $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->dedicatedMentorRepository = $this->buildMockOfInterface(DedicatedMentorRepository::class);
        $this->service = new ExecuteQueryTaskOnDedicatedMentee($this->dedicatedMentorRepository);
        
        $this->dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
        $this->dedicatedMentorRepository->expects($this->any())
                ->method('aDedicatedMentorOfPersonnel')
                ->with($this->personnelId, $this->dedicatedMentorId)
                ->willReturn($this->dedicatedMentor);
        
        $this->task = $this->buildMockOfInterface(QueryTaskOnDedicatedMenteeExecutableByDedicatedMentor::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->personnelId, $this->dedicatedMentorId, $this->task, $this->payload);
    }
    public function test_execute_dedicatedMentorExecuteQuery()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('executeQueryTaskOnDedicatedMentee')
                ->with($this->task, $this->payload);
        $this->execute();
    }
}
