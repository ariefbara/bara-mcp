<?php

namespace Participant\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\{
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class TeamFileInfoFinder implements IFileInfoFinder
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
    protected $teamId;

    public function __construct(FileInfoRepository $fileInfoRepository, string $teamId)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->teamId = $teamId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->fileInfoRepository->fileInfoOfTeam($this->teamId, $fileInfoId);
    }

}
