<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Mission;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Task\Dependency\Firm\Program\Mission\MissionCommentFilter;
use Query\Domain\Task\InProgram\ViewMissionCommentListTask;
use Query\Domain\Task\InProgram\ViewMissionCommentTask;
use Query\Domain\Task\PaginationPayload;

class MissionCommentController extends ClientParticipantBaseController
{
    
    public function showAll($programParticipationId)
    {
        $missionCommentRepository = $this->em->getRepository(MissionComment::class);
        
        $pagination = new PaginationPayload($this->getPage(), $this->getPageSize());
        $payload = (new MissionCommentFilter($pagination))
                ->setOrder($this->stripTagQueryRequest('order'));
        
        $task = new ViewMissionCommentListTask($missionCommentRepository, $payload);
        $this->executeQueryTaskInProgram($programParticipationId, $task);
        
        $result = [];
        $result['total'] = count($task->result);
        foreach ($task->result as $missionComment) {
            $result['list'][] = $this->arrayDataOfMissionComment($missionComment);
        }
        return $this->listQueryResponse($result);
    }
    public function show($programParticipationId, $id)
    {
        $missionCommentRepository = $this->em->getRepository(MissionComment::class);
        $task = new ViewMissionCommentTask($missionCommentRepository, $id);
        
        $this->executeQueryTaskInProgram($programParticipationId, $task);
        return $this->singleQueryResponse($this->arrayDataOfMissionComment($task->result));
    }
    
    protected function arrayDataOfMissionComment(MissionComment $missionComment): array
    {
        return [
            'id' => $missionComment->getId(),
            'message' => $missionComment->getMessage(),
            'rolePaths' => $missionComment->getRolePathsJson(),
            'userName' => $missionComment->getUserName(),
            'modifiedTime' => $missionComment->getModifiedTimeString(),
            'repliedMessage' => $this->arrayDataOfReplyMessage($missionComment->getRepliedComment()),
            'mission' => $this->arrayDataOfMission($missionComment->getMission()),
        ];
    }
    protected function arrayDataOfReplyMessage(?MissionComment $repliedComment): ?array
    {
        return empty($repliedComment) ? null : [
            'id' => $repliedComment->getId(),
            'message' => $repliedComment->getMessage(),
            'rolePaths' => $repliedComment->getRolePathsJson(),
            'userName' => $repliedComment->getUserName(),
            'modifiedTime' => $repliedComment->getModifiedTimeString(),
        ];
    }
    protected function arrayDataOfMission(?Mission $mission): ?array
    {
        return empty($mission) ? null : [
            'id' => $mission->getId(),
            'name' => $mission->getName(),
        ];
    }
    
}
