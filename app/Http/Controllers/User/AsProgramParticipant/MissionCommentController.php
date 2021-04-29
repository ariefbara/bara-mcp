<?php

namespace App\Http\Controllers\User\AsProgramParticipant;

use Firm\Application\Service\User\ProgramParticipant\ReplyMissionComment;
use Firm\Application\Service\User\ProgramParticipant\SubmitMissionComment;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Query\Application\Service\User\AsProgramParticipant\ViewMissionComment;
use Query\Domain\Model\Firm\Program\Mission\MissionComment as MissionComment2;

class MissionCommentController extends AsProgramParticipantBaseController
{
    protected function getMissionCommentData()
    {
        $message = $this->stripTagsInputRequest('message');
        return new MissionCommentData($message);
    }

    public function submit($programId, $missionId)
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        $missionCommentRepository = $this->em->getRepository(MissionComment::class);
        $service = new SubmitMissionComment(
                $this->userParticipantFirmRepository(), $missionRepository, $missionCommentRepository);
        
        $missionCommentId = $service->execute($this->userId(), $programId, $missionId, $this->getMissionCommentData());
        
        $missionComment = $this->buildViewService()
                ->showById($this->userId(), $programId, $missionCommentId);
        return $this->commandCreatedResponse($this->arrayDataOfMissionComment($missionComment));
    }

    public function reply($programId, $missionCommentId)
    {
        $missionCommentRepository = $this->em->getRepository(MissionComment::class);
        $service = new ReplyMissionComment($this->userParticipantFirmRepository(), $missionCommentRepository);
        
        $replyId = $service->execute($this->userId(), $programId, $missionCommentId, $this->getMissionCommentData());
        
        $missionComment = $this->buildViewService()
                ->showById($this->userId(), $programId, $replyId);
        return $this->commandCreatedResponse($this->arrayDataOfMissionComment($missionComment));
    }

    public function show($programId, $missionCommentId)
    {
        $missionComment = $this->buildViewService()
                ->showById($this->userId(), $programId, $missionCommentId);
        return $this->singleQueryResponse($this->arrayDataOfMissionComment($missionComment));
    }

    public function showAll($programId, $missionId)
    {
        $missionComments = $this->buildViewService()
                ->showAll($this->userId(), $programId, $missionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($missionComments);
        foreach ($missionComments as $missionComment) {
            $result['list'][] = $this->arrayDataOfMissionComment($missionComment);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function buildViewService()
    {
        $missionCommentRepository = $this->em->getRepository(MissionComment2::class);
        return new ViewMissionComment($this->userParticipantQueryRepository(), $missionCommentRepository);
    }
    protected function arrayDataOfMissionComment(MissionComment2 $missionComment): array
    {
        return [
            'id' => $missionComment->getId(),
            'message' => $missionComment->getMessage(),
            'rolePaths' => $missionComment->getRolePathsJson(),
            'userName' => $missionComment->getUserName(),
            'modifiedTime' => $missionComment->getModifiedTimeString(),
            'repliedMessage' => $this->arrayDataOfReplyMessage($missionComment->getRepliedComment()),
        ];
    }
    protected function arrayDataOfReplyMessage(?MissionComment2 $repliedComment): ?array
    {
        return empty($repliedComment) ? null : [
            'id' => $repliedComment->getId(),
            'message' => $repliedComment->getMessage(),
            'rolePaths' => $repliedComment->getRolePathsJson(),
            'userName' => $repliedComment->getUserName(),
            'modifiedTime' => $repliedComment->getModifiedTimeString(),
        ];
    }

}
 