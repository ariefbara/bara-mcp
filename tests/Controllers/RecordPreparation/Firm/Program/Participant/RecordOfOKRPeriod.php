<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use DateTime;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfOKRPeriod implements Record
{
    /**
     * 
     * @var RecordOfParticipant|null
     */
    public $participant;
    public $id;
    public $name;
    public $description;
    public $startDate;
    public $endDate;
    public $status;
    public $cancelled;
    
    public function __construct(?RecordOfParticipant $participant, $index)
    {
        $this->participant = $participant;
        $this->id = "okr-period-$index-id";
        $this->name = "okr period $index name";
        $this->description = "okr period $index description";
        $this->startDate = (new DateTime("-$index months"))->format('Y-m-d');
        $this->endDate = (new DateTime("+$index months"))->format('Y-m-d');
        $this->status = OKRPeriodApprovalStatus::UNCONCLUDED;
        $this->cancelled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Participant_id' => isset($this->participant) ? $this->participant->id : null,
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'status' => $this->status,
            'cancelled' => $this->cancelled,
        ];
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection): void
    {
        $connection->table('OKRPeriod')->insert($this->toArrayForDbEntry());
    }

}
