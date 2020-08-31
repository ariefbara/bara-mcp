<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\ProgramInterface;
use DateTimeImmutable;
use Resources\ {
    DateTimeImmutableBuilder,
    Exception\RegularException
};

class Registrant
{
    
    /**
     *
     * @var string
     */
    protected $programId;

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

    public function __construct(ProgramInterface $program, string $id)
    {
        $this->programId = $program->getId();
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
    
    public function isUnconcludedRegistrationToProgram(ProgramInterface $program): bool
    {
        return !$this->isConcluded() && $this->programId === $program->getId();
    }

}
