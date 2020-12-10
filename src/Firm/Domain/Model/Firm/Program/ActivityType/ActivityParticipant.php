<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType;

use Firm\Domain\ {
    Model\Firm\FeedbackForm,
    Model\Firm\Program\ActivityType,
    Model\Firm\Program\MeetingType\CanAttendMeeting,
    Model\Firm\Program\MeetingType\Meeting,
    Service\ActivityTypeDataProvider
};
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ {
    ActivityParticipantPriviledge,
    ActivityParticipantType
};

class ActivityParticipant
{

    /**
     *
     * @var ActivityType
     */
    protected $activityType;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityParticipantType
     */
    protected $participantType;

    /**
     *
     * @var ActivityParticipantPriviledge
     */
    protected $participantPriviledge;

    /**
     *
     * @var FeedbackForm
     */
    protected $reportForm;
    
    /**
     *
     * @var bool
     */
    protected $disabled;
    
    function __construct(ActivityType $activityType, string $id, ActivityParticipantData $activityParticipantData)
    {
        $this->activityType = $activityType;
        $this->id = $id;
        $this->participantType = new ActivityParticipantType($activityParticipantData->getParticipantType());
        $this->participantPriviledge = new ActivityParticipantPriviledge(
                $activityParticipantData->getCanInitiate(), $activityParticipantData->getCanAttend());
        $this->reportForm = $activityParticipantData->getReportForm();
        $this->disabled = false;

        if (isset($this->reportForm) && !$this->reportForm->belongsToSameFirmAs($this->activityType)) {
            $errorDetail = "forbidden: can only assignt feedback form in your firm";
            throw RegularException::forbidden($errorDetail);
        }
    }
    
    public function update(ActivityTypeDataProvider $activityTypeDataProvider): void
    {
        $activityParticipantData = $activityTypeDataProvider
                ->pullActivityParticipantDataCorrespondWithType($this->participantType->getParticipantType());
        if (empty($activityParticipantData)) {
            $this->disabled = true;
        } else {
            $this->disabled = false;
            $this->participantPriviledge = new ActivityParticipantPriviledge(
                    $activityParticipantData->getCanInitiate(), $activityParticipantData->getCanAttend());
            $this->reportForm = $activityParticipantData->getReportForm();
        }
    }

    public function roleCorrespondWithUser(CanAttendMeeting $user): bool
    {
        return $user->roleCorrespondWith($this->participantType);
    }

    public function setUserAsInitiatorInMeeting(Meeting $meeting, CanAttendMeeting $user): void
    {
        if (!$this->roleCorrespondWithUser($user)) {
            return;
        }
        if (!$this->participantPriviledge->canInitiate()) {
            $errorDetail = "forbidden: user type cannot initiate this meeting";
            throw RegularException::forbidden($errorDetail);
        }
        $meeting->setInitiator($this, $user);
    }

    public function addUserAsAttendeeInMeeting(Meeting $meeting, CanAttendMeeting $user): void
    {
        if (!$this->roleCorrespondWithUser($user)) {
            return;
        }
        if (!$this->participantPriviledge->canAttend()) {
            $errorDetail = "forbidden: user type cannot attend this meeting";
            throw RegularException::forbidden($errorDetail);
        }
        $meeting->addAttendee($this, $user);
    }

}
