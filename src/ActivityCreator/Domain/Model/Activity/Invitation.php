<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\{
    DependencyModel\Firm\Manager,
    DependencyModel\Firm\Personnel\Consultant,
    DependencyModel\Firm\Personnel\Coordinator,
    DependencyModel\Firm\Program\Participant,
    Model\Activity,
    service\ActivityDataProvider
};

class Invitation
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
    protected $removed;

    /**
     *
     * @var ManagerInvitation|null
     */
    protected $managerInvitation;

    /**
     *
     * @var CoordinatorInvitation|null
     */
    protected $coordinatorInvitation;

    /**
     *
     * @var ConsultantInvitation|null
     */
    protected $consultantInvitation;

    /**
     *
     * @var ParticipantInvitation|null
     */
    protected $participantInvitation;

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct(Activity $activity, string $id)
    {
        $this->activity = $activity;
        $this->id = $id;
        $this->willAttend = null;
        $this->attended = null;
        $this->removed = false;
    }

    public static function inviteManager(Activity $activity, string $id, Manager $manager): self
    {
        $invitation = new static($activity, $id);
        $invitation->managerInvitation = new ManagerInvitation($invitation, $id, $manager);
        return $invitation;
    }

    public static function inviteCoordinator(Activity $activity, string $id, Coordinator $coordinator): self
    {
        $invitation = new static($activity, $id);
        $invitation->coordinatorInvitation = new CoordinatorInvitation($invitation, $id, $coordinator);
        return $invitation;
    }

    public static function inviteConsultant(Activity $activity, string $id, Consultant $consultant): self
    {
        $invitation = new static($activity, $id);
        $invitation->consultantInvitation = new ConsultantInvitation($invitation, $id, $consultant);
        return $invitation;
    }

    public static function inviteParticipant(Activity $activity, string $id, Participant $participant): self
    {
        $invitation = new static($activity, $id);
        $invitation->participantInvitation = new ParticipantInvitation($invitation, $id, $participant);
        return $invitation;
    }

    public function removeIfNotAppearInList(ActivityDataProvider $activityDataProvider): void
    {
        if (isset($this->managerInvitation)) {
            $this->managerInvitation->removeIfNotApprearInList($activityDataProvider);
        } elseif (isset($this->coordinatorInvitation)) {
            $this->coordinatorInvitation->removeIfNotApprearInList($activityDataProvider);
        } elseif (isset($this->consultantInvitation)) {
            $this->consultantInvitation->removeIfNotApprearInList($activityDataProvider);
        } elseif (isset($this->participantInvitation)) {
            $this->participantInvitation->removeIfNotApprearInList($activityDataProvider);
        }
    }
    
    public function remove(): void
    {
        $this->removed = true;
    }

    public function isNonRemovedInvitationCorrespondWithManager(Manager $manager): bool
    {
        return isset($this->managerInvitation) ?
                !$this->removed && $this->managerInvitation->managerEquals($manager) : false;
    }

    public function isNonRemovedInvitationCorrespondWithCoordinator(Coordinator $coordinator): bool
    {
        return isset($this->coordinatorInvitation) ?
                !$this->removed && $this->coordinatorInvitation->CoordinatorEquals($coordinator) : false;
    }

    public function isNonRemovedInvitationCorrespondWithConsultant(Consultant $consultant): bool
    {
        return isset($this->consultantInvitation) ?
                !$this->removed && $this->consultantInvitation->consultantEquals($consultant) : false;
    }

    public function isNonRemovedInvitationCorrespondWithParticipant(Participant $participant): bool
    {
        return isset($this->participantInvitation) ?
                !$this->removed && $this->participantInvitation->participantEquals($participant) : false;
    }

}
