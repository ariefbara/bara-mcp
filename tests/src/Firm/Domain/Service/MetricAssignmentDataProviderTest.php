<?php

namespace Firm\Domain\Service;

use Firm\Domain\Model\Firm\Program\ {
    Metric,
    Participant\MetricAssignmentData
};
use Tests\TestBase;

class MetricAssignmentDataProviderTest extends TestBase
{
    protected $metricRepository, $metric;
    protected $startDate;
    protected $endDate;
    protected $dataCollector;
    protected $metricId = "metricId";
    protected $target = 999;


    protected function setUp(): void
    {
        parent::setUp();
        $this->metric = $this->buildMockOfClass(Metric::class);
        $this->metricRepository = $this->buildMockOfInterface(MetricRepository::class);
        $this->metricRepository->expects($this->any())
                ->method("ofId")
                ->with($this->metricId)
                ->willReturn($this->metric);
        
        $this->startDate = new \DateTimeImmutable("+1 month");
        $this->endDate = new \DateTimeImmutable("+8 month");
        
        $this->dataCollector = new TestableMetricAssignmentDataProvider($this->metricRepository, $this->startDate, $this->endDate);
    }
    
    protected function executeAdd()
    {
        $this->dataCollector->add($this->metricId, $this->target);
    }
    public function test_add_attachMetricToCollection()
    {
        $this->executeAdd();
        $this->assertTrue($this->dataCollector->collection->contains($this->metric));
        $this->assertEquals($this->target, $this->dataCollector->collection[$this->metric]);
    }
    public function test_add_alreadyContainSameMetric_override()
    {
        $this->dataCollector->collection->attach($this->metric);
        $this->executeAdd();
        $this->assertEquals(1, $this->dataCollector->collection->count());
        $this->assertEquals($this->target, $this->dataCollector->collection[$this->metric]);
    }
    
    protected function executePullTargetCorrespondWithMetric()
    {
        return $this->dataCollector->pullTargetCorrespondWithMetric($this->metric);
    }
    public function test_pullTargetCorrespondWithMetric_returnTargetMappedToSameMetric()
    {
        $this->dataCollector->add($this->metricId, $this->target);
        $this->assertEquals($this->target, $this->executePullTargetCorrespondWithMetric());
    }
    public function test_pullTargetCorrespondWithMetric_removeMappedMetricFromCollection()
    {
        $this->dataCollector->add($this->metricId, $this->target);
        $this->executePullTargetCorrespondWithMetric();
        $this->assertEmpty($this->dataCollector->collection->count());
    }
    public function test_pullTargetCorrespondWithMetric_noTargetMappedToSameMetric_returnNull()
    {
        $this->assertNull($this->executePullTargetCorrespondWithMetric());
    }
    
    public function test_iterateMetrics_returnArrayOfMetrics()
    {
        $this->dataCollector->collection->attach($this->metric);
        $this->assertEquals([$this->metric], $this->dataCollector->iterateMetrics());
    }
}

class TestableMetricAssignmentDataProvider extends MetricAssignmentDataProvider
{
    public $collection;
}
