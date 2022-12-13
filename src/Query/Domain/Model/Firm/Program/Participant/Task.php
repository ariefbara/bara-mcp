<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\Task\TaskReport;
use SharedContext\Domain\ValueObject\Label;

class Task
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var Label
     */
    protected $label;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $dueDate;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $createdTime;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var TaskReport|null
     */
    protected $taskReport;

    protected function __construct()
    {
        
    }

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }

    public function getDueDate(): DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function getCreatedTime(): DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function getModifiedTime(): DateTimeImmutable
    {
        return $this->modifiedTime;
    }

    public function getTaskReport(): ?TaskReport
    {
        return $this->taskReport;
    }

}
