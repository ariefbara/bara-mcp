<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\FlySystemUploadFileBuilder;
use Personnel\ {
    Application\Service\Firm\Personnel\PersonnelFileUpload,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Personnel\PersonnelFileInfo
};
use Query\ {
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Personnel\PersonnelFileInfoView,
    Domain\Model\Firm\Personnel\PersonnelFileInfo as PersonnelFileInfo2
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;

class FileUploadController extends PersonnelBaseController
{

    public function upload()
    {
        $service = $this->buildUploadService();

        $name = htmlentities($this->request->header('fileName'), ENT_QUOTES, 'UTF-8');
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        $fileInfoData->addFolder("firm_{$this->firmId()}");
        $fileInfoData->addFolder("personnel_{$this->personnelId()}");

        $contents = fopen('php://input', 'r');
        $personnelFileInfoId = $service->execute($this->firmId(), $this->personnelId(), $fileInfoData, $contents);
        if (is_resource($contents)) {
            fclose($contents);
        }
        
        $viewService = $this->buildViewService();
        $personnelFileInfo = $viewService->showById($this->firmId(), $this->personnelId(), $personnelFileInfoId);

        return $this->commandCreatedResponse($this->arrayDataOfPersonnelFileInfo($personnelFileInfo));
    }

    protected function arrayDataOfPersonnelFileInfo(PersonnelFileInfo2 $personnelFileInfo): array
    {
        return [
            "id" => $personnelFileInfo->getId(),
            "path" => $personnelFileInfo->getFullyQualifiedFileName(),
            "size" => $personnelFileInfo->getSize(),
        ];
    }

    protected function buildUploadService()
    {
        $personnelFileInfoRepository = $this->em->getRepository(PersonnelFileInfo::class);
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $uploadFile = FlySystemUploadFileBuilder::build();

        return new PersonnelFileUpload(
                $personnelFileInfoRepository, $personnelRepository, $uploadFile);
    }
    
    protected function buildViewService()
    {
        $personnelFileInfoRepository = $this->em->getRepository(PersonnelFileInfo2::class);
        return new PersonnelFileInfoView($personnelFileInfoRepository);
    }

}
