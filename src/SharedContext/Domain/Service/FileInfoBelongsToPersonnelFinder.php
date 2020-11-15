<?php

namespace SharedContext\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\ {
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class FileInfoBelongsToPersonnelFinder implements IFileInfoFinder
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
    
    function __construct(FileInfoRepository $fileInfoRepository, string $firmId, string $personnelId)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->fileInfoRepository->aFileInfoBelongsToPersonnel($this->firmId, $this->personnelId, $fileInfoId);
    }

}
