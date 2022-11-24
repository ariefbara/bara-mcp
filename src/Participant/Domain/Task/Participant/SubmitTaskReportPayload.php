<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant\Task\TaskReportData;

class SubmitTaskReportPayload
{

    /**
     * 
     * @var string|null
     */
    protected $taskId;

    /**
     * 
     * @var string|null
     */
    protected $content;

    /**
     * 
     * @var string[]
     */
    protected $attachedParticipantFileInfoIdList = [];

    public function attachParticipantFileInfoId(?string $participantFileInfoId)
    {
        $this->attachedParticipantFileInfoIdList[] = $participantFileInfoId;
        return $this;
    }

    public function __construct(?string $taskId, ?string $content)
    {
        $this->taskId = $taskId;
        $this->content = $content;
    }

    public function getTaskId(): ?string
    {
        return $this->taskId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getAttachedParticipantFileInfoIdList(): array
    {
        return $this->attachedParticipantFileInfoIdList;
    }

}
