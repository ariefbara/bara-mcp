<?php

namespace Firm\Application\Service\Firm;

use Firm\Application\Service\FirmRepository;
use SharedContext\Domain\ {
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};

class UploadFirmFile
{

    /**
     *
     * @var FirmFileInfoRepository
     */
    protected $firmFileInfoRepository;

    /**
     *
     * @var FirmRepository
     */
    protected $firmRepository;

    /**
     *
     * @var UploadFile
     */
    protected $uploadFile;

    public function __construct(
            FirmFileInfoRepository $firmFileInfoRepository, FirmRepository $firmRepository, UploadFile $uploadFile)
    {
        $this->firmFileInfoRepository = $firmFileInfoRepository;
        $this->firmRepository = $firmRepository;
        $this->uploadFile = $uploadFile;
    }
    
    public function execute(string $firmId, FileInfoData $fileInfoData, $contents): string
    {
        $id = $this->firmFileInfoRepository->nextIdentity();
        $firmFileInfo = $this->firmRepository->ofId($firmId)->createFileInfo($id, $fileInfoData);
        $this->firmFileInfoRepository->add($firmFileInfo);
        
        $this->uploadFile->execute($firmFileInfo, $contents);
        return $id;
    }

}
