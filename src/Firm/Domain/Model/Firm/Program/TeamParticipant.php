<?php

namespace Firm\Domain\Model\Firm\Program;

class TeamParticipant
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
    protected $teamId;

    public function __construct(Participant $participant, string $id, string $teamId)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->teamId = $teamId;
    }

    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return $registrant->correspondWithTeam($this->teamId);
    }

}
