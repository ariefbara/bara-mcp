<?php

namespace Query\Domain\Model\Shared\FormRecord\AttachmentFieldRecord;

use Query\Domain\Model\Shared\ {
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

    function getAttachmentFieldRecord(): AttachmentFieldRecord
    {
        return $this->attachmentFieldRecord;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getFileInfo(): FileInfo
    {
        return $this->fileInfo;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
    }
    
    public function getFileLocation(): ?string
    {
        return $this->fileInfo->getFullyQualifiedFileName();
    }

}
