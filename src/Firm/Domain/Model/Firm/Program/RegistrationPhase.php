<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\DateInterval,
    ValidationRule,
    ValidationService
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
     * @var string
     */
    protected $name;

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

    protected function setName(string $name): void
    {
        $errorDetail = "bad requst: registration phase name required";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    function __construct(Program $program, string $id, RegistrationPhaseData $registrationPhaseData)
    {
        $this->program = $program;
        $this->id = $id;
        $this->setName($registrationPhaseData->getName());
        $this->startEndDate = new DateInterval($registrationPhaseData->getStartDate(),
                $registrationPhaseData->getEndDate());
        $this->removed = false;
    }

    public function update(RegistrationPhaseData $registrationPhaseData): void
    {
        $this->setName($registrationPhaseData->getName());
        $this->startEndDate = new DateInterval($registrationPhaseData->getStartDate(),
                $registrationPhaseData->getEndDate());
    }

    public function remove(): void
    {
        $this->removed = true;
    }
    
    public function isOpen(): bool
    {
        return !$this->removed && $this->startEndDate->contain(DateTimeImmutableBuilder::buildYmdHisAccuracy());
    }

}
