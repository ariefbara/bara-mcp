<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfProgram,
    Record
};

class RecordOfActivity implements Record
{
    /**
     *
     * @var RecordOfActivityType
     */
    public $activityType;
    public $id;
    public $name;
    public $description;
    public $location;
    public $note;
    public $cancelled;
    public $createdTime;
    public $startDateTime;
    public $endDateTime;
    
    function __construct(RecordOfActivityType $activityType, $index)
    {
        $this->activityType = $activityType;
        $this->id = "activity-$index-id";
        $this->name = "activity $index name";
        $this->description = "activity $index description";
        $this->location = "activity $index location";
        $this->note = "activity $index note";
        $this->cancelled = false;
        $this->createdTime = (new \DateTimeImmutable())->format("Y-m-d H:i:s");
        $this->startDateTime = (new \DateTimeImmutable("+24 hours"))->format("Y-m-d H:i:s");
        $this->endDateTime = (new \DateTimeImmutable("+28 hours"))->format("Y-m-d H:i:s");
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "ActivityType_id" => $this->activityType->id,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "location" => $this->location,
            "note" => $this->note,
            "cancelled" => $this->cancelled,
            "createdTime" => $this->createdTime,
            "startDateTime" => $this->startDateTime,
            "endDateTime" => $this->endDateTime,
        ];
    }

}
