<?php

namespace Query\Domain\Model\User;

use Query\Domain\Model\ {
    Firm\Program,
    Firm\Program\Participant,
    User
};

class UserParticipant
{

    /**
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function __construct()
    {
        ;
    }

    public function getProgram(): Program
    {
        return $this->participant->getProgram();
    }

    public function getEnrolledTimeString(): string
    {
        return $this->participant->getEnrolledTimeString();
    }

    public function isActive(): bool
    {
        return $this->participant->isActive();
    }

    public function getNote(): ?string
    {
        return $this->participant->getNote();
    }

}
