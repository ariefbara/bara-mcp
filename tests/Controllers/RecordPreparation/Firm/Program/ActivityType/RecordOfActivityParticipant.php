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
    public $disabled;
    
    function __construct(RecordOfActivityType $activityType, ?RecordOfFeedbackForm $feedbackForm, $index)
    {
        $this->activityType = $activityType;
        $this->feedbackForm = $feedbackForm;
        $this->id = "activityParticipant-$index-id";
        $this->participantType = "coordinator";
        $this->canInitiate = true;
        $this->canAttend = true;
        $this->disabled = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "ActivityType_id" => $this->activityType->id,
            "FeedbackForm_id" => isset($this->feedbackForm)? $this->feedbackForm->id: null,
            "id" => $this->id,
            "participantType" => $this->participantType,
            "canInitiate" => $this->canInitiate,
            "canAttend" => $this->canAttend,
            "disabled" => $this->disabled,
        ];
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection): void
    {
        $connection->table('ActivityParticipant')->insert($this->toArrayForDbEntry());
    }

}
