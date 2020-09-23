<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\ {
    Record,
    RecordOfFirm
};

class RecordOfTeam implements Record
{

    /**
     *
     * @var RecordOfFirm
     */
    public $firm;

    /**
     *
     * @var RecordOfClient
     */
    public $creator;
    public $id;
    public $name;
    public $createdTime;

    public function __construct(RecordOfFirm $firm, RecordOfClient $creator, $index)
    {
        $this->firm = $firm;
        $this->creator = $creator;
        $this->id = "team-$index-id";
        $this->name = "team $index name";
        $this->createdTime = (new DateTimeImmutable())->format("Y-m-d H:i:s");
    }

    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "Client_idOfCreator" => $this->creator->id,
            "id" => $this->id,
            "name" => $this->name,
            "createdTime" => $this->createdTime,
        ];
    }

}
