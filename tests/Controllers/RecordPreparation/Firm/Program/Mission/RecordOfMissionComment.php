<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Mission;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfMissionComment implements Record
{
    /**
     * 
     * @var RecordOfMission|null
     */
    public $mission;
    /**
     * 
     * @var RecordOfMissionComment|null
     */
    public $repliedComment;
    public $id;
    public $message;
    public $rolePaths;
    public $userId;
    public $userName;
    public $modifiedTime;
    
    public function __construct(?RecordOfMission $mission, ?RecordOfMissionComment $repliedComment, $index)
    {
        $this->mission = $mission;
        $this->repliedComment = $repliedComment;
        $this->id = "mission-comment-$index-id";
        $this->message = "mission comment $index message";
        $this->rolePaths = json_encode(['participant' => 'partiicpant-id']);
        $this->userId = 'user-id';
        $this->userName = 'user name';
        $this->modifiedTime = (new \DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
    }

    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('MissionComment')->insert($this->toArrayForDbEntry());
    }
    
    public function toArrayForDbEntry()
    {
        return [
            'Mission_id' => isset($this->mission) ? $this->mission->id: null,
            'MissionComment_idToReply' => isset($this->repliedComment) ? $this->repliedComment->id: null,
            'id' => $this->id,
            'message' => $this->message,
            'rolePaths' => $this->rolePaths,
            'userId' => $this->userId,
            'userName' => $this->userName,
            'modifiedTime' => $this->modifiedTime,
        ];
    }

}
