<?php

namespace Participant\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\ {
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class UserFileInfoFinder implements IFileInfoFinder
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

    public function __construct(FileInfoRepository $fileInfoRepository, string $userId)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->userId = $userId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->fileInfoRepository->fileInfoOfUser($this->userId, $fileInfoId);
    }

}
