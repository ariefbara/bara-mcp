<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\{
    Client,
    Firm\Program
};
use DateTimeImmutable;
use Resources\Exception\RegularException;

class ProgramRegistration
{

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $appliedTime;

    /**
     *
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var string
     */
    protected $note = null;
    function getProgram(): Program
    {
        return $this->program;
    }
    function isConcluded(): bool
    {
        return $this->concluded;
    }

    function __construct(Client $client, $id, Program $program)
    {
        if (!$program->canAcceptRegistration()) {
            $errorDetail = "forbidden: program can't accept registration";
            throw RegularException::forbidden($errorDetail);
        }
        $this->client = $client;
        $this->id = $id;
        $this->program = $program;
        $this->appliedTime = new DateTimeImmutable();
        $this->concluded = false;
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
