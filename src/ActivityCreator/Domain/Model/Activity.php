<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\{
    DependencyModel\Firm\Manager,
    DependencyModel\Firm\Personnel\Consultant,
    DependencyModel\Firm\Personnel\Coordinator,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\ActivityType,
    DependencyModel\Firm\Program\Participant,
    Model\Activity\Invitation,
    service\ActivityDataProvider
};
use DateTimeImmutable;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Resources\{
    DateTimeImmutableBuilder,
    Domain\Model\EntityContainEvents,
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException,
    Uuid,
    ValidationRule,
    ValidationService
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Activity extends EntityContainEvents
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityType
     */
    protected $activityType;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string|null
     */
    protected $description;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var string|null
     */
    protected $location;

    /**
     *
     * @var string|null
     */
    protected $note;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $createdTime;

    /**
     *
     * @var ArrayCollection
     */
    protected $invitations;

    protected function setName(string $name): void
    {
        $errorDetail = "bad request: activity name is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function setStartEndTime(?DateTimeImmutable $startTime, ?DateTimeImmutable $endTime)
    {
        if (!isset($startTime)) {
            $errorDetail = "bad request: activity start time is mandatory";
            throw RegularException::badRequest($errorDetail);
        }
        if (!isset($endTime)) {
            $errorDetail = "bad request: activity end time is mandatory";
            throw RegularException::badRequest($errorDetail);
        }
        $this->startEndTime = new DateTimeInterval($startTime, $endTime);
    }

    function __construct(Program $program, string $id, ActivityType $activityType,
            ActivityDataProvider $activityDataProvider)
    {
        $this->program = $program;
        $this->id = $id;
        $this->activityType = $activityType;
        $this->setName($activityDataProvider->getName());
        $this->description = $activityDataProvider->getDescription();
        $this->setStartEndTime($activityDataProvider->getStartTime(), $activityDataProvider->getEndTime());
        $this->location = $activityDataProvider->getLocation();
        $this->note = $activityDataProvider->getNote();
        $this->cancelled = false;
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();

        $this->invitations = new ArrayCollection();
        $this->addInvitation($activityDataProvider);
    }

    public function update(ActivityDataProvider $activityDataProvider): void
    {
        $this->setName($activityDataProvider->getName());
        $this->description = $activityDataProvider->getDescription();
        $this->setStartEndTime($activityDataProvider->getStartTime(), $activityDataProvider->getEndTime());
        $this->location = $activityDataProvider->getLocation();
        $this->note = $activityDataProvider->getNote();

        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("removed", false));
        foreach ($this->invitations->matching($criteria)->getIterator() as $invitation) {
            $invitation->removeIfNotAppearInList($activityDataProvider);
        }

        foreach ($activityDataProvider->iterateInvitedManagerList() as $manager) {
            $p = function (Invitation $invitation) use ($manager) {
                return $invitation->isNonRemovedInvitationCorrespondWithManager($manager);
            };
            if (empty($this->invitations->filter($p)->count())) {
                $this->addInvitationToManager($manager);
            }
        }
        foreach ($activityDataProvider->iterateInvitedCoordinatorList() as $coordinator) {
            $p = function (Invitation $invitation) use ($coordinator) {
                return $invitation->isNonRemovedInvitationCorrespondWithCoordinator($coordinator);
            };
            if (empty($this->invitations->filter($p)->count())) {
                $this->addInvitationToCoordinator($coordinator);
            }
        }
        foreach ($activityDataProvider->iterateInvitedConsultantList() as $consultant) {
            $p = function (Invitation $invitation) use ($consultant) {
                return $invitation->isNonRemovedInvitationCorrespondWithConsultant($consultant);
            };
            if (empty($this->invitations->filter($p)->count())) {
                $this->addInvitationToConsultant($consultant);
            }
        }
        foreach ($activityDataProvider->iterateInvitedParticipantList() as $participant) {
            $p = function (Invitation $invitation) use ($participant) {
                return $invitation->isNonRemovedInvitationCorrespondWithParticipant($participant);
            };
            if (empty($this->invitations->filter($p)->count())) {
                $this->addInvitationToParticipant($participant);
            }
        }
    }

    protected function addInvitation(ActivityDataProvider $activityDataProvider): void
    {
        foreach ($activityDataProvider->iterateInvitedManagerList() as $manager) {
            $this->addInvitationToManager($manager);
        }
        foreach ($activityDataProvider->iterateInvitedCoordinatorList() as $coordinator) {
            $this->addInvitationToCoordinator($coordinator);
        }
        foreach ($activityDataProvider->iterateInvitedConsultantList() as $consultant) {
            $this->addInvitationToConsultant($consultant);
        }
        foreach ($activityDataProvider->iterateInvitedParticipantList() as $participant) {
            $this->addInvitationToParticipant($participant);
        }
    }

    protected function addInvitationToManager(Manager $manager): void
    {
        if (!$this->activityType->canInvite(new ActivityParticipantType(ActivityParticipantType::MANAGER))) {
            $errorDetail = "forbidden: cannot invite manager role";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$manager->belongsToSameFirmAs($this->program)) {
            $errorDetail = "forbidden: unable to invite manager from different firm";
            throw RegularException::forbidden($errorDetail);
        }
        $id = Uuid::generateUuid4();
        $invitation = Invitation::inviteManager($this, $id, $manager);
        $this->invitations->add($invitation);
    }

    protected function addInvitationToCoordinator(Coordinator $coordinator): void
    {
        if (!$this->activityType->canInvite(new ActivityParticipantType(ActivityParticipantType::COORDINATOR))) {
            $errorDetail = "forbidden: cannot invite coordinator role";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$coordinator->belongsToProgram($this->program)) {
            $errorDetail = "forbidden: unable to invite coordinator from different program";
            throw RegularException::forbidden($errorDetail);
        }
        $id = Uuid::generateUuid4();
        $invitation = Invitation::inviteCoordinator($this, $id, $coordinator);
        $this->invitations->add($invitation);
    }

    protected function addInvitationToConsultant(Consultant $consultant): void
    {
        if (!$this->activityType->canInvite(new ActivityParticipantType(ActivityParticipantType::CONSULTANT))) {
            $errorDetail = "forbidden: cannot invite consultant role";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$consultant->belongsToProgram($this->program)) {
            $errorDetail = "forbidden: unable to invite consultant from different program";
            throw RegularException::forbidden($errorDetail);
        }
        $id = Uuid::generateUuid4();
        $invitation = Invitation::inviteConsultant($this, $id, $consultant);
        $this->invitations->add($invitation);
    }

    protected function addInvitationToParticipant(Participant $participant): void
    {
        if (!$this->activityType->canInvite(new ActivityParticipantType(ActivityParticipantType::PARTICIPANT))) {
            $errorDetail = "forbidden: cannot invite participant role";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$participant->belongsToProgram($this->program)) {
            $errorDetail = "forbidden: unable to invite participant from different program";
            throw RegularException::forbidden($errorDetail);
        }
        $id = Uuid::generateUuid4();
        $invitation = Invitation::inviteParticipant($this, $id, $participant);
        $this->invitations->add($invitation);
    }

}
