<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\ActivityType;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfActivityType,
    Firm\RecordOfFeedbackForm,
    Record
};

class RecordOfActivityParticipant implements Record
{
    /**
     *
     * @var RecordOfActivityType
     */
    public $activityType;
    /**
     *
     * @var RecordOfFeedbackForm
     */
    public $feedbackForm;
    public $id;
    public $participantType;
    public $canInitiate;
    public $canAttend;
    
    function __construct(RecordOfActivityType $activityType, RecordOfFeedbackForm $feedbackForm, $index)
    {
        $this->activityType = $activityType;
        $this->feedbackForm = $feedbackForm;
        $this->id = "activityParticipant-$index-id";
        $this->participantType = "coordinator";
        $this->canInitiate = true;
        $this->canAttend = true;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "ActivityType_id" => $this->activityType->id,
            "FeedbackForm_id" => $this->feedbackForm->id,
            "id" => $this->id,
            "participantType" => $this->participantType,
            "canInitiate" => $this->canInitiate,
            "canAttend" => $this->canAttend,
        ];
    }

}
