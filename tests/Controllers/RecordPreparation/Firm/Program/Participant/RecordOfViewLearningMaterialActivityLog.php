<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Mission\RecordOfLearningMaterial,
    Firm\Program\RecordOfParticipant,
    Record,
    Shared\RecordOfActivityLog
};

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

}
