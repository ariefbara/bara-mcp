<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Task\Dependency\MetricRepository;

class ViewMetricSummaryTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var MetricRepository
     */
    protected $metricRepository;
    public $result;

    public function __construct(MetricRepository $metricRepository)
    {
        $this->metricRepository = $metricRepository;
    }

    public function execute(string $participantId): void
    {
        $this->result = $this->metricRepository->metricSummaryOfParticipant($participantId);
    }

}
