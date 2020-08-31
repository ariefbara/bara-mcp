<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\ {
    Firm\Program,
    User
};

class UserRegistrant
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
     * @var User
     */
    protected $user;

    /**
     *
     * @var Registrant
     */
    protected $registrant;

    public function getId(): string
    {
        return $this->id;
    }
    
    public function getUserId(): string
    {
        return $this->user->getId();
    }

    protected function __construct()
    {
        ;
    }
    
    public function accept(): void
    {
        $this->registrant->accept();
    }

    public function reject(): void
    {
        $this->registrant->reject();
    }

    public function createParticipant(string $userParticipantId): UserParticipant
    {
        return new UserParticipant($this->program, $userParticipantId, $this->user);
    }

    public function userEquals(User $user): bool
    {
        return $this->user === $user;
    }

}
