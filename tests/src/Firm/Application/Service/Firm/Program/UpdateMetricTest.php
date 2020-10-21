<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\ {
    Metric,
    MetricData
};
use Tests\TestBase;

class UpdateMetricTest extends TestBase
{
    protected $metricRepository, $metric;
    protected $service;
    protected $programId = "programId", $metricId = "metricId", $metricData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->metric = $this->buildMockOfClass(Metric::class);
        $this->metricRepository = $this->buildMockOfInterface(MetricRepository::class);
        $this->metricRepository->expects($this->any())
                ->method("aMetricInProgram")
                ->with($this->programId, $this->metricId)
                ->willReturn($this->metric);
        
        $this->service = new UpdateMetric($this->metricRepository);
        
        $this->metricData = $this->buildMockOfClass(MetricData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programId, $this->metricId, $this->metricData);
    }
    public function test_execute_updateMetric()
    {
        $this->metric->expects($this->once())
                ->method("update")
                ->with($this->metricData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->metricRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
