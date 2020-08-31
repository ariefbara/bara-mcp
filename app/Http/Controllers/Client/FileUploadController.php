<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\FlySystemUploadFileBuilder;
use Client\ {
    Application\Service\Client\UploadClientFile,
    Domain\Model\Client,
    Domain\Model\Client\ClientFileInfo as ClientFileInfo2
};
use Query\ {
    Application\Service\Firm\Client\ViewClientFileInfo,
    Domain\Model\Firm\Client\ClientFileInfo
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;

class FileUploadController extends ClientBaseController
{

    public function upload()
    {
        $service = $this->buildUploadService();
        
        $name = strip_tags($this->request->header('fileName'));
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        $fileInfoData->addFolder("client_{$this->clientId()}");

        $contents = fopen('php://input', 'r');
        
        $clientFileInfoId = $service->execute($this->firmId(), $this->clientId(), $fileInfoData, $contents);
        
        if (is_resource($contents)) {
            fclose($contents);
        }
        

        $viewService = $this->buildViewService();
        $clientFileInfo = $viewService->showById($this->firmId(), $this->clientId(), $clientFileInfoId);
        
        return $this->commandCreatedResponse($this->arrayDataOfClientFileInfo($clientFileInfo));
    }

    protected function arrayDataOfClientFileInfo(ClientFileInfo $clientFileInfo): array
    {
        return [
            "id" => $clientFileInfo->getId(),
            "path" => $clientFileInfo->getFullyQualifiedFileName(),
            "size" => $clientFileInfo->getSize(),
        ];
    }

    protected function buildUploadService()
    {
        $clientFileInfoRepository = $this->em->getRepository(ClientFileInfo2::class);
        $clientRepository = $this->em->getRepository(Client::class);
        $uploadFile = FlySystemUploadFileBuilder::build();

        return new UploadClientFile($clientFileInfoRepository, $clientRepository, $uploadFile);
    }

    protected function buildViewService()
    {
        $clientFileInfoRepository = $this->em->getRepository(ClientFileInfo::class);
        return new ViewClientFileInfo($clientFileInfoRepository);
    }

}
