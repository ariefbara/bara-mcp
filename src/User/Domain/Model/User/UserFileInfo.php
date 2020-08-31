<?php

namespace User\Domain\Model\User;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use User\Domain\Model\User;

class UserFileInfo
{

    /**
     *
     * @var User
     */
    protected $user;

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

    function __construct(User $user, string $id, FileInfoData $fileInfoData)
    {
        $this->user = $user;
        $this->id = $id;
        $this->fileInfo = new FileInfo($id, $fileInfoData);
        $this->removed = false;
    }
    
    public function uploadContents(UploadFile $uploadFile, $contents): void
    {
        $uploadFile->execute($this->fileInfo, $contents);
    }

}
