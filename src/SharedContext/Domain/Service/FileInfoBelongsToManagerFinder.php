<?php

namespace SharedContext\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\ {
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class FileInfoBelongsToManagerFinder implements IFileInfoFinder
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
    protected $managerId;

    function __construct(FileInfoRepository $fileInfoRepository, string $firmId, string $managerId)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->firmId = $firmId;
        $this->managerId = $managerId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->fileInfoRepository->aFileInfoBelongsToManager($this->firmId, $this->managerId, $fileInfoId);
    }

}
