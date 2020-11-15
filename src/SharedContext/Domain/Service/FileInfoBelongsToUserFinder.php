<?php

namespace SharedContext\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\ {
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class FileInfoBelongsToUserFinder implements IFileInfoFinder
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
    protected $userId;

    function __construct(FileInfoRepository $fileInfoRepository, string $userId)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->userId = $userId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->fileInfoRepository->aFileInfoBelongsToUser($this->userId, $fileInfoId);
    }

}
