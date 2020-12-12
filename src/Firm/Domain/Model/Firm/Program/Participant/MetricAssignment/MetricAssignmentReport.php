<?php

namespace Firm\Domain\Model\Firm\Program\Participant\MetricAssignment;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\AssetInProgram;
use Firm\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Resources\Exception\RegularException;

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
     * @var bool|null
     */
    protected $approved;

    /**
     *
     * @var string|null
     */
    protected $note;

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
        $this->assertUndecided();
        $this->approved = true;
    }
    
    public function reject(?string $note): void
    {
        $this->assertUndecided();
        $this->approved = false;
        $this->note = $note;
    }
    
    protected function assertUndecided(): void
    {
        if ($this->approved !== null) {
            $errorDetail = "forbidden: unable to alter approval decision";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
