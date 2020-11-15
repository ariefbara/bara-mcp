<?php

namespace SharedContext\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\ {
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class FileInfoBelongsToClientFinder implements IFileInfoFinder
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
    protected $clientId;

    function __construct(FileInfoRepository $fileInfoRepository, string $firmId, string $clientId)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->firmId = $firmId;
        $this->clientId = $clientId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->fileInfoRepository->aFileInfoBelongsToClient($this->firmId, $this->clientId, $fileInfoId);
    }

}
