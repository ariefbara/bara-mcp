<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program\MetricData
};

class AddMetric
{
    /**
     *
     * @var MetricRepository
     */
    protected $metricRepository;
    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;
    
    public function __construct(MetricRepository $metricRepository, ProgramRepository $programRepository)
    {
        $this->metricRepository = $metricRepository;
        $this->programRepository = $programRepository;
    }
    
    public function execute(string $firmId, string $programId, MetricData $metricData): string
    {
        $id = $this->metricRepository->nextIdentity();
        $metric = $this->programRepository->ofId($firmId, $programId)->addMetric($id, $metricData);
        $this->metricRepository->add($metric);
        return $id;
    }

}
