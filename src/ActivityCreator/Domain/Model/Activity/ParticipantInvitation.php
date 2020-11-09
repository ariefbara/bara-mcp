<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\Participant,
    service\ActivityDataProvider
};

class ParticipantInvitation
{

    /**
     *
     * @var Invitation
     */
    protected $invitation;

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

    function __construct(Invitation $invitation, string $id, Participant $participant)
    {
        $this->invitation = $invitation;
        $this->id = $id;
        $this->participant = $participant;
    }

    public function removeIfNotApprearInList(ActivityDataProvider $activityDataProvider): void
    {
        if (!$activityDataProvider->containParticipant($this->participant)) {
            $this->invitation->remove();
        }
    }
    
    public function participantEquals(Participant $participant): bool
    {
        return $this->participant === $participant;
    }

}
