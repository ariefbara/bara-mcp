<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    DependencyModel\Firm\Personnel\Consultant,
    DependencyModel\Firm\Personnel\Coordinator,
    DependencyModel\Firm\Program\ActivityType\ActivityParticipant,
    DependencyModel\Firm\Program\Participant,
    Model\Activity,
    Model\Activity\Invitee\ConsultantInvitee,
    Model\Activity\Invitee\CoordinatorInvitee,
    Model\Activity\Invitee\ManagerInvitee,
    Model\Activity\Invitee\ParticipantInvitee,
    Model\CanReceiveInvitation
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

    function __construct(Activity $activity, string $id, ActivityParticipant $activityParticipant, CanReceiveInvitation $recipient)
    {
        $this->activity = $activity;
        $this->id = $id;
        $this->activityParticipant = $activityParticipant;
        $this->willAttend = null;
        $this->attended = null;
        $this->invitationCancelled = false;
        
        $recipient->registerAsInviteeRecipient($this);
    }

    public function cancelInvitation(): void
    {
        $this->invitationCancelled = true;
    }

    public function reinvite(): void
    {
        $this->invitationCancelled = false;
    }
    
    public function correspondWithRecipient(CanReceiveInvitation $recipient): bool
    {
        if (isset($this->managerInvitee)) {
            return $this->managerInvitee->managerEquals($recipient);
        } elseif (isset ($this->coordinatorInvitee)) {
            return $this->coordinatorInvitee->coordinatorEquals($recipient);
        } elseif (isset ($this->consultantInvitee)) {
            return $this->consultantInvitee->consultantEquals($recipient);
        } elseif (isset ($this->participantInvitee)) {
            return $this->participantInvitee->participantEquals($recipient);
        }
    }

    public function registerCoordinatorAsRecipient(Coordinator $coordinator): void
    {
        $this->coordinatorInvitee = new CoordinatorInvitee($this, $this->id, $coordinator);
    }

    public function registerManagerAsRecipient(Manager $manager): void
    {
        $this->managerInvitee = new ManagerInvitee($this, $this->id, $manager);
    }

    public function registerConsultantAsRecipient(Consultant $consultant): void
    {
        $this->consultantInvitee = new ConsultantInvitee($this, $this->id, $consultant);
    }

    public function registerParticipantAsRecipient(Participant $participant): void
    {
        $this->participantInvitee = new ParticipantInvitee($this, $this->id, $participant);
    }

}
