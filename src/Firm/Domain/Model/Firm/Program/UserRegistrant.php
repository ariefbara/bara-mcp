<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\User;

class UserRegistrant
{
    /**
     *
     * @var Registrant
     */
    protected $registrant;
    
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

    protected function __construct()
    {
        ;
    }
    
    public function createParticipant(Program $program, string $participantId): Participant
    {
        return Participant::participantForUser($program, $participantId, $this->user);
    }
    
    public function userEquals(User $user): bool
    {
        return $this->user === $user;
    }

}
