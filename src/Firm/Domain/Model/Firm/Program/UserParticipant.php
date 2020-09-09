<?php

namespace Firm\Domain\Model\Firm\Program;

class UserParticipant
{

    /**
     *
     * @var Participant
     */
    protected $participant;

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
    
    public function __construct(Participant $participant, string $id, string $userId)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->userId = $userId;
    }
    
    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return $registrant->correspondWithUser($this->userId);
    }
    
}
