<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use App\Http\Controllers\FlySystemUploadFileBuilder;
use Query\ {
    Application\Service\Firm\Team\ViewTeamFileInfo,
    Domain\Model\Firm\Team\TeamFileInfo
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Team\ {
    Application\Service\Team\UploadTeamFile,
    Domain\Model\Team\Member,
    Domain\Model\Team\TeamFileInfo as TeamFileInfo2
};

class FileUploadController extends AsTeamMemberBaseController
{
    public function upload($teamId)
    {
        $service = $this->buildUploadService();
        
        $name = $this->request->header('fileName');
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        $fileInfoData->addFolder("client_{$this->clientId()}");
        $fileInfoData->addFolder("teamMembership_{$teamId}");

        $contents = fopen('php://input', 'r');
        $teamFileInfoId = $service->execute($this->firmId(), $this->clientId(), $teamId, $fileInfoData, $contents);
        
        if (is_resource($contents)) {
            fclose($contents);
        }
        
        $viewService = $this->buildViewService();
        $teamFileInfo = $viewService->showById($teamId, $teamFileInfoId);
        return $this->commandCreatedResponse($this->arrayDataOfTeamFileInfo($teamFileInfo));
    }
    
    protected function arrayDataOfTeamFileInfo(TeamFileInfo $teamFileInfo): array
    {
        return [
            "id" => $teamFileInfo->getId(),
            "path" => $teamFileInfo->getFullyQualifiedFileName(),
            "size" => $teamFileInfo->getSize(),
        ];
    }
    protected function buildViewService()
    {
        $teamFileInfoRepository = $this->em->getRepository(TeamFileInfo::class);
        return new ViewTeamFileInfo($teamFileInfoRepository);
    }
    protected function buildUploadService()
    {
        $teamFileInfoRepository = $this->em->getRepository(TeamFileInfo2::class);
        $memberRepository = $this->em->getRepository(Member::class);
        $uploadFile = FlySystemUploadFileBuilder::build();
        return new UploadTeamFile($teamFileInfoRepository, $memberRepository, $uploadFile);
    }
}
