<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Metric;

class ViewMetric
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

    /**
     * 
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return Metric[]
     */
    public function showAll(string $programId, int $page, int $pageSize)
    {
        return $this->metricRepository->allMetricsInProgram($programId, $page, $pageSize);
    }

    public function showById(string $programId, string $metricId): Metric
    {
        return $this->metricRepository->aMetricInProgram($programId, $metricId);
    }

}
