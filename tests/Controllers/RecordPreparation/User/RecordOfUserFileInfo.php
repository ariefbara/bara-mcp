<?php

namespace Tests\Controllers\RecordPreparation\User;

use Tests\Controllers\RecordPreparation\ {
    Record,
    RecordOfUser,
    Shared\RecordOfFileInfo
};

class RecordOfUserFileInfo implements Record
{
    /**
     *
     * @var RecordOfUser
     */
    public $user;

    /**
     *
     * @var RecordOfFileInfo
     */
    public $fileInfo;
    public $id, $removed = false;

    public function __construct(RecordOfUser $user, RecordOfFileInfo $fileInfo)
    {
        $this->user = $user;
        $this->fileInfo = $fileInfo;
        $this->id = $fileInfo->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'User_id' => $this->user->id,
            'FileInfo_id' => $this->fileInfo->id,
            'id' => $this->id,
            'removed' => $this->removed,
        ];
    }

}
