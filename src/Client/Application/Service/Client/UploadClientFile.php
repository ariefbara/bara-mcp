<?php

namespace Client\Application\Service\Client;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Client\Application\Service\ClientRepository;

class UploadClientFile
{

    /**
     *
     * @var ClientFileInfoRepository
     */
    protected $clientFileInfoRepository;

    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     *
     * @var UploadFile
     */
    protected $uploadFile;

    function __construct(
            ClientFileInfoRepository $clientFileInfoRepository, ClientRepository $clientRepository, UploadFile $uploadFile)
    {
        $this->clientFileInfoRepository = $clientFileInfoRepository;
        $this->clientRepository = $clientRepository;
        $this->uploadFile = $uploadFile;
    }

    public function execute(string $firmId, string $clientId, FileInfoData $fileInfoData, $contents): string
    {
        $id = $this->clientFileInfoRepository->nextIdentity();
        $clientFileInfo = $this->clientRepository->ofId($firmId, $clientId)->createClientFileInfo($id, $fileInfoData);
        
        $clientFileInfo->uploadContents($this->uploadFile, $contents);
        $this->clientFileInfoRepository->add($clientFileInfo);
        
        return $id;
    }

}
