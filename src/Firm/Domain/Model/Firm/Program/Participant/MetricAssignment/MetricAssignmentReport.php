<?php

namespace Firm\Domain\Model\Firm\Program\Participant\MetricAssignment;

use Firm\Domain\Model\Firm\ {
    Program,
    Program\AssetInProgram,
    Program\Participant\MetricAssignment
};

class MetricAssignmentReport implements AssetInProgram
{

    /**
     *
     * @var MetricAssignment
     */
    protected $metricAssignment;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $approved;

    /**
     *
     * @var bool
     */
    protected $removed;

    protected function __construct()
    {
        
    }
    
    public function belongsToProgram(Program $program): bool
    {
        return $this->metricAssignment->belongsToProgram($program);
    }
    
    public function approve(): void
    {
        $this->approved = true;
    }

}
