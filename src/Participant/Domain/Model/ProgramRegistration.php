<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Program;
use Resources\ {
    DateTimeImmutableBuilder,
    Exception\RegularException
};

class ProgramRegistration
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
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $registeredTime;

    /**
     *
     * @var string||null
     */
    protected $note;

    public function isConcluded(): bool
    {
        return $this->concluded;
    }

    public function __construct(Program $program, string $id)
    {
        $this->program = $program;
        $this->id = $id;
        $this->concluded = false;
        $this->registeredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->note = null;
    }

    public function cancel(): void
    {
        if ($this->concluded) {
            $errorDetail = 'forbidden: program registration already concluded';
            throw RegularException::forbidden($errorDetail);
        }
        $this->concluded = true;
        $this->note = 'cancelled';
    }
    
    public function isUnconcludedRegistrationToProgram(Program $program): bool
    {
        return !$this->isConcluded() && $this->program === $program;
    }

}
