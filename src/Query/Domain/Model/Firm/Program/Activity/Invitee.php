<?php

namespace Query\Domain\Model\Firm\Program\Activity;

use Query\Domain\Model\Firm\{
    Manager\ManagerInvitee,
    Program\Activity,
    Program\Activity\Invitee\InviteeReport,
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
     * @var bool
     */
    protected $anInitiator;

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
    protected $cancelled;

    /**
     *
     * @var InviteeReport|null
     */
    protected $report;

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

    function isAnInitiator(): bool
    {
        return $this->anInitiator;
    }

    function isWillAttend(): ?bool
    {
        return $this->willAttend;
    }

    function isAttended(): ?bool
    {
        return $this->attended;
    }

    function isCancelled(): bool
    {
        return $this->cancelled;
    }

    function getReport(): ?InviteeReport
    {
        return $this->report;
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
