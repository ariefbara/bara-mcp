<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Activity;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\ActivityType\RecordOfActivityParticipant,
    Firm\Program\RecordOfActivity,
    Record
};

class RecordOfInvitee implements Record
{
    /**
     *
     * @var RecordOfActivity
     */
    public $activity;
    /**
     *
     * @var RecordOfActivityParticipant
     */
    public $activityParticipant;
    public $id;
    public $invitationCancelled;
    public $willAttend;
    public $attended;
    
    
    function __construct(
            RecordOfActivity $activity, RecordOfActivityParticipant $activityParticipant, $index)
    {
        $this->activity = $activity;
        $this->activityParticipant = $activityParticipant;
        $this->id = "invitee-$index-id";
        $this->invitationCancelled = false;
        $this->willAttend = null;
        $this->attended = null;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Activity_id" => $this->activity->id,
            "ActivityParticipant_id" => $this->activityParticipant->id,
            "id" => $this->id,
            "invitationCancelled" => $this->invitationCancelled,
            "willAttend" => $this->willAttend,
            "attended" => $this->attended,
        ];
    }

}
