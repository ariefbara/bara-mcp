<?php

namespace Firm\Domain\Model\Firm\Program\Participant\OKRPeriod;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\AssetInProgram;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod;

class Objective implements AssetInProgram
{

    /**
     * 
     * @var OKRPeriod
     */
    protected $okrPeriod;

    /**
     * 
     * @var string
     */
    protected $id;

    protected function __construct()
    {
        
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->okrPeriod->belongsToProgram($program);
    }

}
