<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Activity;

use Illuminate\Database\Connection;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfInvitee implements Record
{
    /**
     *
     * @var RecordOfActivity
     */
    public $activity;
    /**
     *
     * @var RecordOfActivityParticipant|null
     */
    public $activityParticipant;
    public $id;
    public $anInitiator;
    public $cancelled;
    public $willAttend;
    public $attended;
    
    
    function __construct(
            ?RecordOfActivity $activity, ?RecordOfActivityParticipant $activityParticipant, $index)
    {
        $this->activity = isset($activity)? $activity: new RecordOfActivity(null, $index);
        $this->activityParticipant = $activityParticipant;
        $this->id = "invitee-$index-id";
        $this->anInitiator = false;
        $this->cancelled = false;
        $this->willAttend = null;
        $this->attended = null;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Activity_id" => $this->activity->id,
            "ActivityParticipant_id" => isset($this->activityParticipant)? $this->activityParticipant->id: null,
            "id" => $this->id,
            "anInitiator" => $this->anInitiator,
            "cancelled" => $this->cancelled,
            "willAttend" => $this->willAttend,
            "attended" => $this->attended,
        ];
    }
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("Invitee")->insert($this->toArrayForDbEntry());
    }

}
