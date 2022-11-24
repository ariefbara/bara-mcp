<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\Model\SharedEntity\FileInfoData;

class UploadFilePayload
{

    /**
     * 
     * @var FileInfoData
     */
    protected $fileInfoData;
    protected $contents;
    public $uploadedFileInfoId;

    public function __construct(FileInfoData $fileInfoData, $contents)
    {
        $this->fileInfoData = $fileInfoData;
        $this->contents = $contents;
    }

    public function getFileInfoData(): FileInfoData
    {
        return $this->fileInfoData;
    }

    public function getContents()
    {
        return $this->contents;
    }

}
