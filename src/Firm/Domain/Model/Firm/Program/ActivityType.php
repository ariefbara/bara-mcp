<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\ {
    Model\AssetBelongsToFirm,
    Model\Firm,
    Model\Firm\Program,
    Model\Firm\Program\ActivityType\ActivityParticipant,
    Model\Firm\Program\MeetingType\CanAttendMeeting,
    Model\Firm\Program\MeetingType\Meeting,
    Model\Firm\Program\MeetingType\MeetingData,
    Service\ActivityTypeDataProvider
};
use Resources\ {
    Exception\RegularException,
    Uuid,
    ValidationRule,
    ValidationService
};

class ActivityType implements AssetBelongsToFirm, AssetInProgram
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
     * @var ArrayCollection
     */
    protected $participants;

    protected function setName(string $name): void
    {
        $errorDetail = "bad request: activity type name is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    public function __construct(Program $program, string $id, ActivityTypeDataProvider $activityTypeDataProvider)
    {
        $this->program = $program;
        $this->id = $id;
        $this->setName($activityTypeDataProvider->getName());
        $this->description = $activityTypeDataProvider->getDescription();

        $this->participants = new ArrayCollection();
        foreach ($activityTypeDataProvider->iterateActivityParticipantData() as $activityParticipantData) {
            $id = Uuid::generateUuid4();
            $activityParticipant = new ActivityParticipant($this, $id, $activityParticipantData);
            $this->participants->add($activityParticipant);
        }
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }
    
    public function belongsToProgram(Program $program): bool
    {
        return $this->program === $program;
    }

    public function createMeeting(string $meetingId, MeetingData $meetingData, CanAttendMeeting $initiator): Meeting
    {
        return new Meeting($this, $meetingId, $meetingData, $initiator);
    }
    
    public function setUserAsInitiatorInMeeting(Meeting $meeting, CanAttendMeeting $user): void
    {
        $this->findAttendeeSetupCorrespondWithUserOrDie($user)->setUserAsInitiatorInMeeting($meeting, $user);
    }

    public function addUserAsAttendeeInMeeting(Meeting $meeting, CanAttendMeeting $user): void
    {
        if (!$user->canInvolvedInProgram($this->program)) {
            $errorDetail = "forbidden: user cannot be involved in program";
            throw RegularException::forbidden($errorDetail);
        }
        $this->findAttendeeSetupCorrespondWithUserOrDie($user)->addUserAsAttendeeInMeeting($meeting, $user);
    }

    protected function findAttendeeSetupCorrespondWithUserOrDie(CanAttendMeeting $user): ActivityParticipant
    {
        $p = function (ActivityParticipant $attendeeSetup) use ($user) {
            return $attendeeSetup->roleCorrespondWithUser($user);
        };
        $meetingSetup = $this->participants->filter($p)->first();
        if (empty($meetingSetup)) {
            $errorDetail = "forbidden: this user type is not allowed to involved in meeting type";
            throw RegularException::forbidden($errorDetail);
        }
        return $meetingSetup;
    }

}
