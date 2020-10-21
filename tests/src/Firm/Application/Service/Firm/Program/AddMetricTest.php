<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\MetricData
};
use Tests\TestBase;

class AddMetricTest extends TestBase
{
    protected $metricRepository, $nextId = "nextId";
    protected $programRepository, $program;
    protected $service;
    protected $firmId = "firmId", $programId = "programId", $metricData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->metricRepository = $this->buildMockOfInterface(MetricRepository::class);
        $this->metricRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->programId)
                ->willReturn($this->program);
        
        $this->service = new AddMetric($this->metricRepository, $this->programRepository);
        
        $this->metricData = $this->buildMockOfClass(MetricData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->programId, $this->metricData);
    }
    public function test_execute_addMetricToRepository()
    {
        $this->program->expects($this->once())
                ->method("addMetric")
                ->with($this->nextId, $this->metricData);
        
        $this->metricRepository->expects($this->once())
                ->method("add");
        
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
