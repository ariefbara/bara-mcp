<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\Task\TaskReport;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;
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
    
    protected function setDueDate(?\DateTimeImmutable $dueDate): void
    {
        if (!is_null($dueDate) && $dueDate <= new DateTimeImmutable('tomorrow')) {
            throw RegularException::badRequest('if set, due date must be an upcoming date');
        }
        $this->dueDate = $dueDate;
    }

    public function __construct(Participant $participant, string $id, TaskData $data)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->label = new Label($data->getLabelData());
        $this->setDueDate($data->getDueDate());
        $this->cancelled = false;
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }
    
    //
    protected function assertNotCancelled()
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('task already cancelled, no further changes allowed');
        }
    }

    //
    public function update(TaskData $data): void
    {
        $this->assertNotCancelled();
        $previousLabel = $this->label;
        $previousDueDate = $this->dueDate;
        $this->label = $this->label->update($data->getLabelData());
        $this->setDueDate($data->getDueDate());
        if (!$previousLabel->sameValueAs($this->label) || $previousDueDate != $this->dueDate) {
            $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        }
    }

    public function cancel(): void
    {
        $this->cancelled = true;
    }
    
    //
    public function approveReport(): void
    {
        $this->assertNotCancelled();
        $this->taskReport->approve();
    }
    
    public function askForReportRevision(): void
    {
        $this->assertNotCancelled();
        $this->taskReport->askForRevision();
    }
}
