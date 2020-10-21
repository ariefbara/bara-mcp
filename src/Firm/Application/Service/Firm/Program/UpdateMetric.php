<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\MetricData;

class UpdateMetric
{
    /**
     *
     * @var MetricRepository
     */
    protected $metricRepository;
    
    public function __construct(MetricRepository $metricRepository)
    {
        $this->metricRepository = $metricRepository;
    }
    
    public function execute(string $programId, string $metricId, MetricData $metricData): void
    {
        $this->metricRepository->aMetricInProgram($programId, $metricId)->update($metricData);
        $this->metricRepository->update();
    }

}
