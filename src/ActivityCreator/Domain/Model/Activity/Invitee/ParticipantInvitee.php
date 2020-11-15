<?php

namespace ActivityCreator\Domain\Model\Activity\Invitee;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\Participant,
    Model\Activity\Invitee,
    Model\CanReceiveInvitation
};

class ParticipantInvitee
{

    /**
     *
     * @var Invitee
     */
    protected $invitee;

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

    function __construct(Invitee $invitee, string $id, Participant $participant)
    {
        $this->invitee = $invitee;
        $this->id = $id;
        $this->participant = $participant;
    }
    
    public function participantEquals(CanReceiveInvitation $participant): bool
    {
        return $this->participant === $participant;
    }

}
