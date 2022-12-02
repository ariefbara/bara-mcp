<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\Task\TaskReport;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\LabelData;

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

    public function __construct(Participant $participant, string $id, LabelData $data)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->label = new Label($data);
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
    public function update(LabelData $data): void
    {
        $this->assertNotCancelled();
        $previousLabel = $this->label;
        $this->label = $this->label->update($data);
        if (!$previousLabel->sameValueAs($this->label)) {
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
