<?php

namespace Personnel\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\{
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class PersonnelFileInfoFinder implements IFileInfoFinder
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
    protected $personnelId;

    public function __construct(FileInfoRepository $fileInfoRepository, string $firmId,
            string $personnelId)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->fileInfoRepository->aFileInfoOfPersonnel($this->firmId, $this->personnelId, $fileInfoId);
    }

}
