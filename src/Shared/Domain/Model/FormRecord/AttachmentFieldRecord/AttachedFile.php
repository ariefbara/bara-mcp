<?php

namespace Shared\Domain\Model\FormRecord\AttachmentFieldRecord;

use Shared\Domain\Model\ {
    FileInfo,
    FormRecord\AttachmentFieldRecord
};

class AttachedFile
{

    /**
     * 
     * @var AttachmentFieldRecord
     */
    protected $attachmentFieldRecord;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * 
     * @var bool
     */
    protected $removed = false;

    function getFileInfo(): FileInfo
    {
        return $this->fileInfo;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    public function __construct(AttachmentFieldRecord $attachmentFieldRecord, string $id, FileInfo $fileInfo)
    {
        $this->attachmentFieldRecord = $attachmentFieldRecord;
        $this->id = $id;
        $this->fileInfo = $fileInfo;
        $this->removed = false;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
