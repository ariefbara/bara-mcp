<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfActivityLog;

class RecordOfViewLearningMaterialActivityLog implements Record
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

    /**
     *
     * @var RecordOfActivityLog
     */
    public $activityLog;
    public $id;

    public function __construct(
            RecordOfParticipant $participant, RecordOfLearningMaterial $learningMaterial,
            RecordOfActivityLog $activityLog)
    {
        $this->participant = $participant;
        $this->learningMaterial = $learningMaterial;
        $this->activityLog = $activityLog;
        $this->id = $activityLog->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "LearningMaterial_id" => $this->learningMaterial->id,
            "ActivityLog_id" => $this->activityLog->id,
            "id" => $this->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->activityLog->insert($connection);
        $connection->table('ViewLearningMaterialActivityLog')->insert($this->toArrayForDbEntry());
    }

}
