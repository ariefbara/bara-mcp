<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\ActivityParticipant;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Service\ActivityTypeDataProvider;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

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
     * @var bool
     */
    protected $disabled;

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
        $this->disabled = false;

        $this->participants = new ArrayCollection();
        $this->addActivityParticipant($activityTypeDataProvider);
    }

    public function update(ActivityTypeDataProvider $activityTypeDataProvider): void
    {
        $this->setName($activityTypeDataProvider->getName());
        $this->description = $activityTypeDataProvider->getDescription();

        foreach ($this->participants->getIterator() as $attendeeSetup) {
            $attendeeSetup->update($activityTypeDataProvider);
        }
        $this->addActivityParticipant($activityTypeDataProvider);
    }

    public function disable(): void
    {
        $this->disabled = true;
    }

    public function enable(): void
    {
        $this->disabled = false;
    }

    protected function addActivityParticipant(ActivityTypeDataProvider $activityTypeDataProvider): void
    {
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

    public function assertUsableInProgram(Program $program): void
    {
        if ($this->disabled || $this->program !== $program) {
            throw RegularException::forbidden('forbidden: unable to use activity type');
        }
    }
    
    public function assertUsableInFirm(Firm $firm): void
    {
        if ($this->disabled || !$this->program->belongsToFirm($firm)) {
            throw RegularException::forbidden('forbidden: unable to use activity type');
        }
    }
    
    public function getActiveAttendeeSetupCorrenspondWithRoleOrDie(ActivityParticipantType $activityParticipantType): ActivityParticipant
    {
        $p = function (ActivityParticipant $activityParticipant) use($activityParticipantType) {
            return $activityParticipant->isActiveTypeCorrespondWithRole($activityParticipantType);
        };
        $activityParticipant = $this->participants->filter($p)->first();
        
        if (empty($activityParticipant)) {
            throw RegularException::notFound('not found: no attendee setup correspond with user role');
        }
        return $activityParticipant;
    }
    
    public function createMeeting(string $meetingId, MeetingData $meetingData): Meeting
    {
        if ($this->disabled) {
            throw RegularException::forbidden('forbidden: inactive activity type');
        }
        return new Meeting($this, $meetingId, $meetingData);
    }
    
    public function inviteAllActiveProgramParticipantsToMeeting(Meeting $meeting): void
    {
        $this->program->inviteAllActiveParticipantsToMeeting($meeting);
    }

}
