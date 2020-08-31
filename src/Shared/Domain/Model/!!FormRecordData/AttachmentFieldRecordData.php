<?php

namespace Shared\Domain\Model\FormRecordData;

use Shared\Domain\Model\FileInfo;

class AttachmentFieldRecordData
{

    /**
     *
     * @var IFileInfoFinder
     */
    protected $fileInfoFinder;

    /**
     *
     * @var string
     */
    protected $attachmentFieldId;

    /**
     *
     * @var array
     */
    protected $fileInfoCollection = [];

    function getAttachmentFieldId(): string
    {
        return $this->attachmentFieldId;
    }

    /**
     * 
     * @return FileInfo[]
     */
    function getFileInfoCollection(): array
    {
        return $this->fileInfoCollection;
    }

    function __construct(IFileInfoFinder $fileInfoFinder, string $attachmentFieldId)
    {
        $this->fileInfoFinder = $fileInfoFinder;
        $this->attachmentFieldId = $attachmentFieldId;
        $this->fileInfoCollection = [];
    }

    public function add(string $fileInfoId): void
    {
        $this->fileInfoCollection[] = $this->fileInfoFinder->ofId($fileInfoId);
    }

}
