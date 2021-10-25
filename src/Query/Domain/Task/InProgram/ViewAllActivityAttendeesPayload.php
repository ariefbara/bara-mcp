<?php

namespace Query\Domain\Task\InProgram;

class ViewAllActivityAttendeesPayload
{

    /**
     * 
     * @var string
     */
    protected $activityId;

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
     * @var bool|null
     */
    protected $cancelledStatus;

    /**
     * 
     * @var bool|null
     */
    protected $attendeedStatus;

    public function getActivityId(): string
    {
        return $this->activityId;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getCancelledStatus(): ?bool
    {
        return $this->cancelledStatus;
    }

    public function getAttendeedStatus(): ?bool
    {
        return $this->attendeedStatus;
    }

    public function __construct(string $activityId, int $page, int $pageSize)
    {
        $this->activityId = $activityId;
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public function setCancelledStatus(?bool $cancelledStatus)
    {
        $this->cancelledStatus = $cancelledStatus;
        return $this;
    }

    public function setAttendeedStatus(?bool $attendeedStatus)
    {
        $this->attendeedStatus = $attendeedStatus;
        return $this;
    }

}
