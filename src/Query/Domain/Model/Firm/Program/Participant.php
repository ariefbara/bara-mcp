<?php

namespace Query\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Query\Domain\Model\{
    Firm\Client\ClientParticipant,
    Firm\Program,
    User\UserParticipant
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
     * @var DateTimeImmutable
     */
    protected $enrolledTime;

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

    /**
     *
     * @var ClientParticipant||null
     */
    protected $clientParticipant;

    /**
     *
     * @var UserParticipant||null
     */
    protected $userParticipant;

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEnrolledTimeString(): string
    {
        return $this->enrolledTime->format('Y-m-d H:i:s');
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getClientParticipant(): ?ClientParticipant
    {
        return $this->clientParticipant;
    }

    public function getUserParticipant(): ?UserParticipant
    {
        return $this->userParticipant;
    }

    protected function __construct()
    {
        ;
    }

}
