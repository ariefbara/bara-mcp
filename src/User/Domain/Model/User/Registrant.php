<?php

namespace User\Domain\Model\User;

use DateTimeImmutable;
use Resources\ {
    DateTimeImmutableBuilder,
    Exception\RegularException
};
use User\Domain\Model\ProgramInterface;

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

}
