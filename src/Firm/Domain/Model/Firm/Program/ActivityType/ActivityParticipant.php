<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType;

use Firm\Domain\Model\Firm\ {
    FeedbackForm,
    Program\ActivityType
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

    function __construct(ActivityType $activityType, string $id, ActivityParticipantData $activityParticipantData)
    {
        $this->activityType = $activityType;
        $this->id = $id;
        $this->participantType = new ActivityParticipantType($activityParticipantData->getParticipantType());
        $this->participantPriviledge = new ActivityParticipantPriviledge(
                $activityParticipantData->getCanInitiate(), $activityParticipantData->getCanAttend());
        $this->reportForm = $activityParticipantData->getReportForm();
        
        if (isset($this->reportForm) && !$this->reportForm->belongsToSameFirmAs($this->activityType)) {
            $errorDetail = "forbidden: can only assignt feedback form in your firm";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
