<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfLearningProgress implements Record
{

    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     * 
     * @var RecordOfLearningMaterial
     */
    public $learningMaterial;
    public $id;
    public $lastModifiedTime;
    public $progressMark;
    public $markAsCompleted;

    public function __construct(RecordOfParticipant $participant, RecordOfLearningMaterial $learningMaterial, $index)
    {
        $this->participant = $participant;
        $this->learningMaterial = $learningMaterial;
        $this->id = "learningProgress-$index-id";
        $this->lastModifiedTime = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $this->progressMark = null;
        $this->markAsCompleted = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Participant_id' => $this->participant->id,
            'LearningMaterial_id' => $this->learningMaterial->id,
            'id' => $this->id,
            'lastModifiedTime' => $this->lastModifiedTime,
            'progressMark' => $this->progressMark,
            'markAsCompleted' => $this->markAsCompleted,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('LearningProgress')->insert($this->toArrayForDbEntry());
    }

}
