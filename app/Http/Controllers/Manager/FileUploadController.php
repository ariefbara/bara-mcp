<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\FlySystemUploadFileBuilder;
use Query\ {
    Application\Service\Firm\Manager\ViewManagerFileInfo,
    Domain\Model\Firm\Manager\ManagerFileInfo
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use User\ {
    Application\Service\Manager\UploadManagerFile,
    Domain\Model\Manager,
    Domain\Model\Manager\ManagerFileInfo as ManagerFileInfo2
};

class FileUploadController extends ManagerBaseController
{

    public function upload()
    {
        $service = $this->buildUploadService();
        
        $name = htmlentities($this->request->header('fileName'), ENT_QUOTES, 'UTF-8');
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        $fileInfoData->addFolder("firm_{$this->firmId()}");
        $fileInfoData->addFolder("manager_{$this->managerId()}");

        $contents = fopen('php://input', 'r');
        $managerFileInfoId = $service->execute($this->firmId(), $this->managerId(), $fileInfoData, $contents);
        
        if (is_resource($contents)) {
            fclose($contents);
        }
        
        $viewService = $this->buildViewService();
        $managerFileInfo = $viewService->showById($this->firmId(), $this->managerId(), $managerFileInfoId);
        return $this->commandCreatedResponse($this->arrayDataOfManagerFileInfo($managerFileInfo));
    }

    protected function arrayDataOfManagerFileInfo(ManagerFileInfo $managerFileInfo): array
    {
        return [
            "id" => $managerFileInfo->getId(),
            "path" => $managerFileInfo->getFullyQualifiedFileName(),
            "size" => $managerFileInfo->getSize(),
        ];
    }

    protected function buildUploadService()
    {
        $managerFileInfoRepository = $this->em->getRepository(ManagerFileInfo2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        $uploadFile = FlySystemUploadFileBuilder::build();
        
        return new UploadManagerFile($managerFileInfoRepository, $managerRepository, $uploadFile);
    }

    protected function buildViewService()
    {
        $managerFileInfoRepository = $this->em->getRepository(ManagerFileInfo::class);
        return new ViewManagerFileInfo($managerFileInfoRepository);
    }

}
