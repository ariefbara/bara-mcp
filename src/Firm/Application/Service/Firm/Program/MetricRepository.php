<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Metric;

interface MetricRepository
{

    public function nextIdentity(): string;

    public function add(Metric $metric): void;

    public function aMetricInProgram(string $programId, string $metricId): Metric;
    
    public function update(): void;
}
