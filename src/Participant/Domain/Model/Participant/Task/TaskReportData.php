<?php

namespace Participant\Domain\Model\Participant\Task;

use Participant\Domain\Model\Participant\ParticipantFileInfo;
use SplObjectStorage;

class TaskReportData
{

    /**
     * 
     * @var string|null
     */
    protected $content;

    /**
     * 
     * @var SplObjectStorage
     */
    protected $attachedParticipantFileInfoList;

    public function attachParticipantFileInfo(ParticipantFileInfo $participantFileInfo)
    {
        $this->attachedParticipantFileInfoList->attach($participantFileInfo);
        return $this;
    }

    public function __construct(?string $content)
    {
        $this->content = $content;
        $this->attachedParticipantFileInfoList = new SplObjectStorage();
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getAttachedParticipantFileInfoList(): SplObjectStorage
    {
        return $this->attachedParticipantFileInfoList;
    }

    public function detachParticipantFileInfo(ParticipantFileInfo $participantFileInfo): bool
    {
        
        if ($this->attachedParticipantFileInfoList->contains($participantFileInfo)) {
            $this->attachedParticipantFileInfoList->detach($participantFileInfo);
            return true;
        }
        return false;
    }

}
