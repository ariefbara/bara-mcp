<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class RecordOfBioSearchFilter implements Record
{
    /**
     * 
     * @var RecordOfFirm
     */
    public $firm;
    public $id;
    public $disabled;
    public $modifiedTime;
    
    public function __construct(RecordOfFirm $firm)
    {
        $this->firm = $firm;
        $this->disabled = false;
        $this->modifiedTime = (new DateTimeImmutable('-1 months'))->format('Y-m-d H:i:s');
    }

    public function toArrayForDbEntry()
    {
        return [
            'Firm_id' => $this->firm->id,
            'id' => $this->firm->id,
            'disabled' => $this->disabled,
            'modifiedTime' => $this->modifiedTime,
        ];
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection): void
    {
        $connection->table('BioSearchFilter')->insert($this->toArrayForDbEntry());
    }

}
