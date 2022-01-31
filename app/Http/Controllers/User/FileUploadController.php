<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ {
    FlySystemUploadFileBuilder,
    User\UserBaseController
};
use Query\ {
    Application\Service\User\ViewUserFileInfo,
    Domain\Model\User\UserFileInfo
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use User\ {
    Application\Service\User\UploadUserFile,
    Domain\Model\User,
    Domain\Model\User\UserFileInfo as UserFileInfo2
};

class FileUploadController extends UserBaseController
{

    public function upload()
    {
        $service = $this->buildUploadService();
        
        $name = $this->request->header('fileName');
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        $fileInfoData->addFolder("user_{$this->userId()}");

        $contents = fopen('php://input', 'r');
        
        $userFileInfoId = $service->execute($this->userId(), $fileInfoData, $contents);
        
        if (is_resource($contents)) {
            fclose($contents);
        }
        

        $viewService = $this->buildViewService();
        $userFileInfo = $viewService->showById($this->userId(), $userFileInfoId);
        
        return $this->commandCreatedResponse($this->arrayDataOfUserFileInfo($userFileInfo));
    }

    protected function arrayDataOfUserFileInfo(UserFileInfo $userFileInfo): array
    {
        return [
            "id" => $userFileInfo->getId(),
            "path" => $userFileInfo->getFullyQualifiedFileName(),
            "size" => $userFileInfo->getSize(),
        ];
    }

    protected function buildUploadService()
    {
        $userFileInfoRepository = $this->em->getRepository(UserFileInfo2::class);
        $userRepository = $this->em->getRepository(User::class);
        $uploadFile = FlySystemUploadFileBuilder::build();

        return new UploadUserFile($userFileInfoRepository, $userRepository, $uploadFile);
    }

    protected function buildViewService()
    {
        $userFileInfoRepository = $this->em->getRepository(UserFileInfo::class);
        return new ViewUserFileInfo($userFileInfoRepository);
    }

}
