<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\Task\TaskReportData;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantFileInfoRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\TaskRepository;

class SubmitTaskReport implements ParticipantTask
{

    /**
     * 
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * 
     * @var ParticipantFileInfoRepository
     */
    protected $participantFileInfoRepository;

    public function __construct(TaskRepository $taskRepository,
            ParticipantFileInfoRepository $participantFileInfoRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->participantFileInfoRepository = $participantFileInfoRepository;
    }

    /**
     * 
     * @param Participant $participant
     * @param SubmitTaskReportPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $taskReportData = new TaskReportData($payload->getContent());
        foreach ($payload->getAttachedParticipantFileInfoIdList() as $id) {
            $participantFileInfo = $this->participantFileInfoRepository->ofId($id);
            $participantFileInfo->assertUsableByParticipant($participant);
            $taskReportData->attachParticipantFileInfo($participantFileInfo);
        }
        
        $task = $this->taskRepository->ofId($payload->getTaskId());
        $task->assertManageableByParticipant($participant);
        $task->submitReport($taskReportData);
    }

}
