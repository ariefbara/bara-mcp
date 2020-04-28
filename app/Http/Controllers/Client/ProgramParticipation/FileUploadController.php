<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\{
    Client\ClientBaseController,
    FlySystemUploadFileBuilder
};
use Client\{
    Application\Service\Client\ProgramParticipation\ProgramParticipationFileUpload,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\ProgramParticipationFileInfo
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
        $programParticipationFileInfo = $service->execute(
                $this->clientId(), $programParticipationId, $fileInfoData, $contents);
        if (is_resource($contents)) {
            fclose($contents);
        }

        return $this->commandCreatedResponse($this->arrayDataOfProgramParticipationFileInfo($programParticipationFileInfo));
    }

    protected function arrayDataOfProgramParticipationFileInfo(ProgramParticipationFileInfo $programParticipationFileInfo): array
    {
        return [
            "id" => $programParticipationFileInfo->getId(),
            "path" => $programParticipationFileInfo->getFullyQualifiedFileName(),
            "size" => $programParticipationFileInfo->getSize(),
        ];
    }

    protected function buildUploadService()
    {
        $programParticipationFileInfoRepository = $this->em->getRepository(ProgramParticipationFileInfo::class);
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        $uploadFile = FlySystemUploadFileBuilder::build();

        return new ProgramParticipationFileUpload(
                $programParticipationFileInfoRepository, $programParticipationRepository, $uploadFile);
    }

}
