<?php

namespace Query\Domain\Model\Firm\Program\Coordinator;

use Query\Domain\Model\Firm\Program\{
    Activity,
    Activity\Invitee,
    Activity\Invitee\InviteeReport,
    ActivityType\ActivityParticipant,
    Coordinator
};

class CoordinatorInvitee
{

    /**
     *
     * @var Coordinator
     */
    protected $coordinator;

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

    function getCoordinator(): Coordinator
    {
        return $this->coordinator;
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

    function getReport(): ?InviteeReport
    {
        return $this->invitee->getReport();
    }

}
