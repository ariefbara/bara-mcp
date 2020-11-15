<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\{
    Activity,
    Activity\Invitee,
    ActivityType\ActivityParticipant,
    Participant
};

class ParticipantInvitee
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
     * @var Invitee
     */
    protected $invitee;

    function getParticipant(): Participant
    {
        return $this->participant;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    function getActivity(): Activity
    {
        return $this->invitee->getActivity();
    }

    function getActivityParticipant(): ActivityParticipant
    {
        return $this->invitee->getActivityParticipant();
    }

    function WillAttend(): ?bool
    {
        return $this->invitee->WillAttend();
    }

    function isAttended(): ?bool
    {
        return $this->invitee->isAttended();
    }

    function isInvitationCancelled(): bool
    {
        return $this->invitee->isInviteeCancelled();
    }

}
