<?php

namespace User\Application\Service\Manager;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};

class UploadManagerFile
{
    /**
     *
     * @var ManagerFileInfoRepository
     */
    protected $managerFileInfoRepository;
    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;
    
    /**
     *
     * @var UploadFile
     */
    protected $uploadFile;
    
    function __construct(ManagerFileInfoRepository $managerFileInfoRepository, ManagerRepository $managerRepository,
            UploadFile $uploadFile)
    {
        $this->managerFileInfoRepository = $managerFileInfoRepository;
        $this->managerRepository = $managerRepository;
        $this->uploadFile = $uploadFile;
    }
    
    public function execute(string $firmId, string $managerId, FileInfoData $fileInfoData, $contents): string
    {
        $id = $this->managerFileInfoRepository->nextIdentity();
        $managerFileInfo = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->saveFileInfo($id, $fileInfoData);
        
        $managerFileInfo->uploadContents($this->uploadFile, $contents);
        $this->managerFileInfoRepository->add($managerFileInfo);
        return $id;
    }

    
}
