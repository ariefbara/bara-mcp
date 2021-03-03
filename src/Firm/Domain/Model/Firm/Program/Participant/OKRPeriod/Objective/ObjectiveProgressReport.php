<?php

namespace Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\AssetInProgram;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;

class ObjectiveProgressReport implements AssetInProgram
{

    /**
     * 
     * @var Objective
     */
    protected $objective;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var OKRPeriodApprovalStatus
     */
    protected $approvalStatus;

    /**
     * 
     * @var bool
     */
    protected $cancelled;
    
    protected function __construct()
    {
        
    }
    protected function assertActive(): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: progress report submission already cancelled');
        }
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->objective->belongsToProgram($program);
    }
    
    public function approve(): void
    {
        $this->assertActive();
        $this->approvalStatus = $this->approvalStatus->approve();
    }
    
    public function reject(): void
    {
        $this->assertActive();
        $this->approvalStatus = $this->approvalStatus->reject();
    }

}
