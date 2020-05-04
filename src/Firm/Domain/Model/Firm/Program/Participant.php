<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program;
use Query\Domain\Model\Client;
use Resources\Exception\RegularException;

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
     * @var string
     */
    protected $note;

    function getId(): string
    {
        return $this->id;
    }

    function getClient(): Client
    {
        return $this->client;
    }

    function __construct(Program $program, $id, Client $client)
    {
        $this->program = $program;
        $this->id = $id;
        $this->client = $client;
        $this->acceptedTime = new DateTimeImmutable();
        $this->active = true;
        $this->note = null;
    }

    public function remove(): void
    {
        if (!$this->active) {
            $errorDetail = 'forbidden: participant already inactive';
            throw RegularException::forbidden($errorDetail);
        }

        $this->active = false;
        $this->note = 'removed';
    }

    public function reActivate(): void
    {
        if ($this->active) {
            $errorDetail = 'forbidden: already an active participant';
            throw RegularException::forbidden($errorDetail);
        }

        $this->active = true;
        $this->note = 'reactivated';
    }

}
