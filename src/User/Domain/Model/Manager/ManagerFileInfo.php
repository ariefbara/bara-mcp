<?php

namespace User\Domain\Model\Manager;

use SharedContext\Domain\{
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use User\Domain\Model\Manager;

class ManagerFileInfo
{

    /**
     *
     * @var Manager
     */
    protected $manager;

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
    protected $removed;

    function __construct(Manager $manager, string $id, FileInfoData $fileInfoData)
    {
        $this->manager = $manager;
        $this->id = $id;
        $this->fileInfo = new FileInfo($id, $fileInfoData);
        $this->removed = false;
    }

    public function uploadContents(UploadFile $uploadFile, $contents): void
    {
        $uploadFile->execute($this->fileInfo, $contents);
    }

}
