<?php

namespace Team\Domain\Model\Team;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Team\Domain\Model\Team;

class TeamFileInfo
{

    /**
     *
     * @var Team
     */
    protected $team;

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

    function __construct(Team $team, string $id, FileInfoData $fileInfoData)
    {
        $this->team = $team;
        $this->id = $id;
        $this->fileInfo = new FileInfo($id, $fileInfoData);
        $this->removed = false;
    }
    
    public function uploadContents(UploadFile $uploadFile, $contents): void
    {
        $uploadFile->execute($this->fileInfo, $contents);
    }

}
