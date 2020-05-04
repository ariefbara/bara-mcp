<?php

namespace Query\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Query\Domain\Model\{
    Client,
    Firm\Program
};

class Participant
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
    protected $acceptedTime;

    /**
     *
     * @var bool
     */
    protected $active = true;

    /**
     *
     * @var string||null
     */
    protected $note;

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

    function getAcceptedTimeString(): string
    {
        return $this->acceptedTime->format('Y-m-d H:i:s');
    }

    function isActive(): bool
    {
        return $this->active;
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
