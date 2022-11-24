<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Participant\Domain\Task\Participant\UploadFile;
use Participant\Domain\Task\Participant\UploadFilePayload;
use Query\Domain\Model\Firm\Program\Participant\ParticipantFileInfo as ParticipantFileInfo2;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Participant\ViewParticipantFileInfoDetail;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use SharedContext\Infrastructure\Persistence\Flysystem\FlysystemFileRepository;

class UploadFileController extends ClientParticipantBaseController
{
    public function upload($programParticipationId)
    {
        $participantFileInfoRepository = $this->em->getRepository(ParticipantFileInfo::class);
        $task = new UploadFile($participantFileInfoRepository, $this->buildFlysistemFileRepository());
        
        $name = $this->request->header('fileName');
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        
        $contents = fopen('php://input', 'r');
        
        $payload = new UploadFilePayload($fileInfoData, $contents);
        $this->executeClientParticipantTask($programParticipationId, $task, $payload);
        
        if (is_resource($contents)) {
            fclose($contents);
        }
        
        //
        $participantFileInfoQueryRepository = $this->em->getRepository(ParticipantFileInfo2::class);
        $queryTask = new ViewParticipantFileInfoDetail($participantFileInfoQueryRepository);
        $queryPayload = new CommonViewDetailPayload($payload->uploadedFileInfoId);
        $this->executeParticipantQueryTask($programParticipationId, $queryTask, $queryPayload);
        
        return $this->commandCreatedResponse($this->arrayDataOfParticipantFileInfo($queryPayload->result));
    }
    
    protected function arrayDataOfParticipantFileInfo(ParticipantFileInfo2 $participantFileInfo): array
    {
        return [
            "id" => $participantFileInfo->getId(),
            "path" => $participantFileInfo->getFullyQualifiedFileName(),
            "size" => $participantFileInfo->getSize(),
        ];
    }
}
