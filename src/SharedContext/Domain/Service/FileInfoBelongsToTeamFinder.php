<?php

namespace SharedContext\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\ {
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class FileInfoBelongsToTeamFinder implements IFileInfoFinder
{

    /**
     *
     * @var FileInfoRepository
     */
    protected $fileInfoRepository;

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $teamId;

    function __construct(FileInfoRepository $fileInfoRepository, string $firmId, string $teamId)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->firmId = $firmId;
        $this->teamId = $teamId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->fileInfoRepository->aFileInfoBelongsToTeam($this->firmId, $this->teamId, $fileInfoId);
    }

}
