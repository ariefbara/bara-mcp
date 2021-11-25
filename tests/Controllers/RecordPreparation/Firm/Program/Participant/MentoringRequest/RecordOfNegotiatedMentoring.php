<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class RecordOfNegotiatedMentoring implements Record
{
    /**
     * 
     * @var RecordOfMentoringRequest
     */
    public $mentoringRequest;
    /**
     * 
     * @var RecordOfMentoring
     */
    public $mentoring;
    public $id;
    
    public function __construct(RecordOfMentoringRequest $mentoringRequest, RecordOfMentoring $mentoring)
    {
        $this->mentoringRequest = $mentoringRequest;
        $this->mentoring = $mentoring;
        $this->id = $mentoring->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'MentoringRequest_id' => $this->mentoringRequest->id,
            'Mentoring_id' => $this->mentoring->id,
            'id' => $this->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->mentoring->insert($connection);
        $connection->table('NegotiatedMentoring')->insert($this->toArrayForDbEntry());
    }

}
