<?php

namespace Query\Domain\Model\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Program\ {
    Activity,
    Activity\Invitee,
    Activity\Invitee\InviteeReport,
    ActivityType\ActivityParticipant,
    Consultant
};

class ConsultantInvitee
{

    /**
     *
     * @var Consultant
     */
    protected $consultant;

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

    function getConsultant(): Consultant
    {
        return $this->consultant;
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
