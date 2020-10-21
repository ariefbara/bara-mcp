<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\FlySystemUploadFileBuilder;
use Firm\ {
    Application\Service\Firm\UploadFirmFile,
    Domain\Model\Firm,
    Domain\Model\Firm\FirmFileInfo as FirmFileInfo2
};
use Query\ {
    Application\Service\Firm\ViewFirmFileInfo,
    Domain\Model\Firm\FirmFileInfo
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;

class UploadFileController extends ManagerBaseController
{
    public function upload()
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildUploadService();
        
        $name = strip_tags($this->request->header('fileName'));
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        $fileInfoData->addFolder("firm_{$this->firmId()}");

        $contents = fopen('php://input', 'r');
        
        $firmFileInfoId = $service->execute($this->firmId(), $fileInfoData);
        
        if (is_resource($contents)) {
            fclose($contents);
        }
        

        $viewService = $this->buildViewService();
        $firmFileInfo = $viewService->showById($this->firmId(), $firmFileInfoId);
        
        return $this->commandCreatedResponse($this->arrayDataOfFirmFileInfo($firmFileInfo));
    }
    
    public function arrayDataOfFirmFileInfo(FirmFileInfo $firmFileInfo): array
    {
        return [
            "id" => $firmFileInfo->getId(),
            "path" => $firmFileInfo->getFullyQualifiedFileName(),
            "size" => $firmFileInfo->getSize(),
        ];
    }
    
    protected function buildViewService()
    {
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo::class);
        return new ViewFirmFileInfo($firmFileInfoRepository);
    }
    
    protected function buildUploadService()
    {
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo2::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        $uploadFile = FlySystemUploadFileBuilder::build();
        return new UploadFirmFile($firmFileInfoRepository, $firmRepository, $uploadFile);
    }
}
