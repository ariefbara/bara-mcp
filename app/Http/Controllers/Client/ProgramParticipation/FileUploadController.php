<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\ {
    Client\ClientBaseController,
    FlySystemUploadFileBuilder
};
use Client\ {
    Application\Service\Client\ProgramParticipation\ParticipantFileUpload,
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\ParticipantFileInfo
};
use Query\ {
    Application\Service\Client\ProgramParticipation\ParticipantFileInfoView,
    Domain\Model\Firm\Program\Participant\ParticipantFileInfo as ParticipantFileInfo2
};
use Shared\Domain\Model\FileInfoData;

class FileUploadController extends ClientBaseController
{

    public function upload($programParticipationId)
    {
        $service = $this->buildUploadService();

        $name = strip_tags($this->request->header('fileName'));
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        $fileInfoData->addFolder("client_{$this->clientId()}");
        $fileInfoData->addFolder("programParticipation_{$programParticipationId}");

        $contents = fopen('php://input', 'r');
        $participantFileInfoId = $service->execute(
                $this->clientId(), $programParticipationId, $fileInfoData, $contents);
        if (is_resource($contents)) {
            fclose($contents);
        }
        
        $viewService = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $participantFileInfo = $viewService->showById($programParticipationCompositionId, $participantFileInfoId);

        return $this->commandCreatedResponse($this->arrayDataOfParticipantFileInfo($participantFileInfo));
    }

    protected function arrayDataOfParticipantFileInfo(ParticipantFileInfo2 $programParticipationFileInfo): array
    {
        return [
            "id" => $programParticipationFileInfo->getId(),
            "path" => $programParticipationFileInfo->getFullyQualifiedFileName(),
            "size" => $programParticipationFileInfo->getSize(),
        ];
    }

    protected function buildUploadService()
    {
        $programParticipationFileInfoRepository = $this->em->getRepository(ParticipantFileInfo::class);
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        $uploadFile = FlySystemUploadFileBuilder::build();

        return new ParticipantFileUpload(
                $programParticipationFileInfoRepository, $programParticipationRepository, $uploadFile);
    }
    
    protected function buildViewService()
    {
        $participantFileInfoRepository = $this->em->getRepository(ParticipantFileInfo2::class);
        return new ParticipantFileInfoView($participantFileInfoRepository);
    }

}
