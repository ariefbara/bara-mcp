<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

class WorksheetFilter
{

    /**
     * 
     * @var int
     */
    protected $page;

    /**
     * 
     * @var int
     */
    protected $pageSize;

    /**
     * 
     * @var string|null
     */
    protected $participantId;

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getParticipantId(): ?string
    {
        return $this->participantId;
    }

    public function __construct(int $page, int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public function setParticipantId(?string $participantId)
    {
        $this->participantId = $participantId;
        return $this;
    }

}
