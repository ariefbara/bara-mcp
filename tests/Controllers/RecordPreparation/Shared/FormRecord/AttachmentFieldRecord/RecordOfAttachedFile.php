<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord\AttachmentFieldRecord;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\FormRecord\RecordOfAttachmentFieldRecord,
    Shared\RecordOfFileInfo
};

class RecordOfAttachedFile implements Record
{

    /**
     *
     * @var RecordOfAttachmentFieldRecord
     */
    public $attachmentFieldRecord;

    /**
     *
     * @var RecordOfFileInfo
     */
    public $fileInfo;
    public $id, $removed;

    function __construct(RecordOfAttachmentFieldRecord $attachmentFieldRecord, RecordOfFileInfo $fileInfo, $index)
    {
        $this->attachmentFieldRecord = $attachmentFieldRecord;
        $this->fileInfo = $fileInfo;
        $this->id = "attached-file-$index-id";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "AttachmentFieldRecord_id" => $this->attachmentFieldRecord->id,
            "FileInfo_id" => $this->fileInfo->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }

}
