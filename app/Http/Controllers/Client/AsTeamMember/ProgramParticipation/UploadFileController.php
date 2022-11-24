<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Participant\Domain\Task\Participant\UploadFile;
use Participant\Domain\Task\Participant\UploadFilePayload;
use Query\Domain\Model\Firm\Program\Participant\ParticipantFileInfo as ParticipantFileInfo2;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Participant\ViewParticipantFileInfoDetail;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;

class UploadFileController extends AsTeamMemberBaseController
{
    public function upload($teamId, $teamProgramParticipationId)
    {
        $participantFileInfoRepository = $this->em->getRepository(ParticipantFileInfo::class);
        $task = new UploadFile($participantFileInfoRepository, $this->buildFlysistemFileRepository());
        
        $name = $this->request->header('fileName');
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        
        $contents = fopen('php://input', 'r');
        
        $payload = new UploadFilePayload($fileInfoData, $contents);
        $this->executeTeamParticipantExtendedTask($teamId, $teamProgramParticipationId, $task, $payload);
        
        if (is_resource($contents)) {
            fclose($contents);
        }
        
        //
        $participantFileInfoQueryRepository = $this->em->getRepository(ParticipantFileInfo2::class);
        $queryTask = new ViewParticipantFileInfoDetail($participantFileInfoQueryRepository);
        $queryPayload = new CommonViewDetailPayload($payload->uploadedFileInfoId);
        $this->executeTeamParticipantExtentedQueryTask($teamId, $teamProgramParticipationId, $queryTask, $queryPayload);
        
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
