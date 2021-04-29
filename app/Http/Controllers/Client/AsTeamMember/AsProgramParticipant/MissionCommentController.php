<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant;

use Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\ReplyMissionComment;
use Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\SubmitMissionComment;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Query\Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant\ViewMissionComment;
use Query\Domain\Model\Firm\Program\Mission\MissionComment as MissionComment2;

class MissionCommentController extends AsProgramParticipantBaseController
{
    protected function getMissionCommentData()
    {
        $message = $this->stripTagsInputRequest('message');
        return new MissionCommentData($message);
    }

    public function submit($teamId, $programId, $missionId)
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        $missionCommentRepository = $this->em->getRepository(MissionComment::class);
        $service = new SubmitMissionComment(
                $this->teamMemberFirmRepository(), $this->teamParticipantFirmRepository(), $missionRepository,
                $missionCommentRepository);
        
        $missionCommentId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $programId, $missionId, $this->getMissionCommentData());
        
        $missionComment = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $teamId, $programId, $missionCommentId);
        return $this->commandCreatedResponse($this->arrayDataOfMissionComment($missionComment));
    }

    public function reply($teamId, $programId, $missionCommentId)
    {
        $missionCommentRepository = $this->em->getRepository(MissionComment::class);
        $service = new ReplyMissionComment(
                $this->teamMemberFirmRepository(), $this->teamParticipantFirmRepository(), $missionCommentRepository);
        
        $replyId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $programId, $missionCommentId, $this->getMissionCommentData());
        
        $missionComment = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $teamId, $programId, $replyId);
        return $this->commandCreatedResponse($this->arrayDataOfMissionComment($missionComment));
    }

    public function show($teamId, $programId, $missionCommentId)
    {
        $missionComment = $this->buildViewService()->showById(
                $this->firmId(), $this->clientId(), $teamId, $programId, $missionCommentId);
        return $this->singleQueryResponse($this->arrayDataOfMissionComment($missionComment));
    }

    public function showAll($teamId, $programId, $missionId)
    {
        $missionComments = $this->buildViewService()->showAll(
                $this->firmId(), $this->clientId(), $teamId, $programId, $missionId, $this->getPage(), $this->getPageSize());
        
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
        return new ViewMissionComment(
                $this->teamMemberQueryRepository(), $this->teamParticipantQueryRepository(), $missionCommentRepository);
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
 