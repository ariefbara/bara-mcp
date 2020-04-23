<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Mission;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\RecordOfMission,
    Record
};

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

}
