<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\AssetInProgram;
use Firm\Domain\Model\Firm\Program\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;

class OKRPeriod implements AssetInProgram
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

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
            throw RegularException::forbidden('forbidden: okr period already cancelled');
        }
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->participant->belongsToProgram($program);
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
