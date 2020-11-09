<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\ {
    Activity,
    Activity\Invitation,
    Participant
};

class ParticipantInvitation
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
     * @var Invitation
     */
    protected $invitation;

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
        return $this->invitation->getActivity();
    }

    function willAttend(): ?bool
    {
        return $this->invitation->willAttend();
    }

    function isAttended(): ?bool
    {
        return $this->invitation->isAttended();
    }

    function isRemoved(): bool
    {
        return $this->invitation->isRemoved();
    }

}
