<?php

namespace Query\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Query\Domain\Model\{
    Client,
    Firm\Program
};

class Registrant
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
     * @var Client
     */
    protected $client;

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
     * @var string||null
     */
    protected $note = null;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getClient(): Client
    {
        return $this->client;
    }

    function getAppliedTimeString(): string
    {
        return $this->appliedTime->format('Y-m-d H:i:s');
    }

    function isConcluded(): bool
    {
        return $this->concluded;
    }

    function getNote(): ?string
    {
        return $this->note;
    }

    protected function __construct()
    {
        ;
    }

}
