<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Participant\Domain\DependencyModel\Firm\Program;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\DateInterval
};

class RegistrationPhase
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var DateInterval
     */
    protected $startEndDate;

    /**
     *
     * @var bool
     */
    protected $removed;
    
    protected function __construct()
    {
        
    }
    
    public function isOpen(): bool
    {
        return !$this->removed && $this->startEndDate->contain(DateTimeImmutableBuilder::buildYmdHisAccuracy());
    }

}
