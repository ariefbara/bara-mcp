<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use DateTimeImmutable;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class RecordOfTeam implements Record
{

    /**
     *
     * @var RecordOfFirm
     */
    public $firm;

    /**
     *
     * @var RecordOfClient|null
     */
    public $creator;
    public $id;
    public $name;
    public $createdTime;

    public function __construct(?RecordOfFirm $firm, ?RecordOfClient $creator, $index)
    {
        $this->firm = isset($firm) ? $firm : new RecordOfFirm($index);
        $this->creator = $creator;
        $this->id = "team-$index-id";
        $this->name = "team $index name";
        $this->createdTime = (new DateTimeImmutable())->format("Y-m-d H:i:s");
    }

    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "Client_idOfCreator" => isset($this->creator) ? $this->creator->id : null,
            "id" => $this->id,
            "name" => $this->name,
            "createdTime" => $this->createdTime,
        ];
    }

    public function persistSelf(Connection $connection): void
    {
        $connection->table("Team")->insert($this->toArrayForDbEntry());
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Team')->insert($this->toArrayForDbEntry());
    }

}
