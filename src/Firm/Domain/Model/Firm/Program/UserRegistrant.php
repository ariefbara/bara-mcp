<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;

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
     * @var string
     */
    protected $userId;

    protected function __construct()
    {
        ;
    }
    
    public function createParticipant(Program $program, string $participantId): Participant
    {
        return Participant::participantForUser($program, $participantId, $this->userId);
    }
    
    public function userIdEquals(string $userId): bool
    {
        return $this->userId === $userId;
    }

}
