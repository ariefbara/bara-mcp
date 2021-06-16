<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Mission;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfLearningMaterial implements Record
{

    /**
     *
     * @var RecordOfMission
     */
    public $mission;
    public $id, $name, $content, $removed = false;

    function __construct(RecordOfMission $mission, $index)
    {
        $this->mission = $mission;
        $this->id = "learningMaterial-$index-id";
        $this->name = "learning material $index name";
        $this->content = "learning material $index content";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Mission_id" => $this->mission->id,
            "id" => $this->id,
            "name" => $this->name,
            "content" => $this->content,
            "removed" => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('LearningMaterial')->insert($this->toArrayForDbEntry());
    }

}
