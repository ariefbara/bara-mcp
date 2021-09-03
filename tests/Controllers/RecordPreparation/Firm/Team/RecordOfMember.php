<?php

namespace Tests\Controllers\RecordPreparation\Firm\Team;

use DateTimeImmutable;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfMember implements Record
{

    /**
     *
     * @var RecordOfTeam
     */
    public $team;

    /**
     *
     * @var RecordOfClient
     */
    public $client;
    public $id;
    public $position;
    public $anAdmin;
    public $active;
    public $joinTime;

    public function __construct(?RecordOfTeam $team, ?RecordOfClient $client, $index)
    {
        $this->team = isset($team)? $team: new RecordOfTeam(null, null, $index);
        $this->client = isset($client)? $client: new RecordOfClient($this->team->firm, $index);
        $this->id = "member-$index-id";
        $this->position = "member $index position";
        $this->anAdmin = true;
        $this->active = true;
        $this->joinTime = (new DateTimeImmutable())->format("Y-m-d H:i:s");
    }

    public function toArrayForDbEntry()
    {
        return [
            "Team_id" => $this->team->id,
            "Client_id" => $this->client->id,
            "id" => $this->id,
            "position" => $this->position,
            "anAdmin" => $this->anAdmin,
            "active" => $this->active,
            "joinTime" => $this->joinTime,
        ];
    }
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("T_Member")->insert($this->toArrayForDbEntry());
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('T_Member')->insert($this->toArrayForDbEntry());
    }

}
