<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\ActivityType,
    DependencyModel\Firm\Program\ActivityType\ActivityParticipant,
    Model\Activity\Invitee,
    service\ActivityDataProvider
};
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\Model\EntityContainEvents,
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException,
    Uuid,
    ValidationRule,
    ValidationService
};

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
    protected $invitees;

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

        $this->invitees = new ArrayCollection();

        $this->activityType->addInviteesToActivity($this, $activityDataProvider);
    }

    public function update(ActivityDataProvider $activityDataProvider): void
    {
        $this->setName($activityDataProvider->getName());
        $this->description = $activityDataProvider->getDescription();
        $this->setStartEndTime($activityDataProvider->getStartTime(), $activityDataProvider->getEndTime());
        $this->location = $activityDataProvider->getLocation();
        $this->note = $activityDataProvider->getNote();
        
        foreach ($this->invitees->getIterator() as $invitee) {
            $invitee->cancelInvitation();
        }

        $this->activityType->addInviteesToActivity($this, $activityDataProvider);
    }
    
    public function addInvitee(CanReceiveInvitation $recipient, ActivityParticipant $activityParticipant): void
    {
        if (!$recipient->canInvolvedInProgram($this->program)) {
            $errorDetail = "forbidden: invitee cannot be involved in program";
            throw RegularException::forbidden($errorDetail);
        }
        if (!empty($invitee = $this->findInviteeCorrespondWithRecipient($recipient))) {
            $invitee->reinvite();
        } else {
            $id = Uuid::generateUuid4();
            $invitee = new Invitee($this, $id, $activityParticipant, $recipient);
            $this->invitees->add($invitee);
        }
    }
    
    protected function findInviteeCorrespondWithRecipient(CanReceiveInvitation $recipient): ?Invitee
    {
        $p = function (Invitee $invitee) use ($recipient) {
            return $invitee->correspondWithRecipient($recipient);
        };
        $invitee = $this->invitees->filter($p)->first();
        return empty($invitee)? null: $invitee;
    }

}
