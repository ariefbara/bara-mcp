<?php

namespace Query\Domain\Model\Firm\Program\Activity;

use Query\Domain\Model\Firm\ {
    Manager\ManagerInvitee,
    Program\Activity,
    Program\ActivityType\ActivityParticipant,
    Program\Consultant\ConsultantInvitee,
    Program\Coordinator\CoordinatorInvitee,
    Program\Participant\ParticipantInvitee
};

class Invitee
{

    /**
     *
     * @var Activity
     */
    protected $activity;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityParticipant
     */
    protected $activityParticipant;

    /**
     *
     * @var bool|null
     */
    protected $willAttend;

    /**
     *
     * @var bool|null
     */
    protected $attended;

    /**
     *
     * @var bool
     */
    protected $invitationCancelled;

    /**
     *
     * @var ManagerInvitee|null
     */
    protected $managerInvitee;

    /**
     *
     * @var CoordinatorInvitee|null
     */
    protected $coordinatorInvitee;

    /**
     *
     * @var ConsultantInvitee|null
     */
    protected $consultantInvitee;

    /**
     *
     * @var ParticipantInvitee|null
     */
    protected $participantInvitee;

    function getActivity(): Activity
    {
        return $this->activity;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getActivityParticipant(): ActivityParticipant
    {
        return $this->activityParticipant;
    }

    function WillAttend(): ?bool
    {
        return $this->willAttend;
    }

    function isAttended(): ?bool
    {
        return $this->attended;
    }

    function isInviteeCancelled(): bool
    {
        return $this->invitationCancelled;
    }

    protected function __construct()
    {
        
    }

    function getManagerInvitee(): ?ManagerInvitee
    {
        return $this->managerInvitee;
    }

    function getCoordinatorInvitee(): ?CoordinatorInvitee
    {
        return $this->coordinatorInvitee;
    }

    function getConsultantInvitee(): ?ConsultantInvitee
    {
        return $this->consultantInvitee;
    }

    function getParticipantInvitee(): ?ParticipantInvitee
    {
        return $this->participantInvitee;
    }

}
