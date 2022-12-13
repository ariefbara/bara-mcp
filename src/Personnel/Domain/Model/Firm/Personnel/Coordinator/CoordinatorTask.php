<?php

namespace Personnel\Domain\Model\Firm\Personnel\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\Task;
use Personnel\Domain\Model\Firm\Program\Participant\TaskData;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\LabelData;

class CoordinatorTask
{

    /**
     * 
     * @var Coordinator
     */
    protected $coordinator;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Task
     */
    protected $task;

    public function __construct(Coordinator $coordinator, Participant $participant, string $id, TaskData $data)
    {
        $this->coordinator = $coordinator;
        $this->id = $id;
        $this->task = new Task($participant, $id, $data);
    }

    public function update(TaskData $data): void
    {
        $this->task->update($data);
    }

    public function cancel(): void
    {
        $this->task->cancel();
    }

    public function assertManageableByCoordinator(Coordinator $coordinator): void
    {
        if ($this->coordinator !== $coordinator) {
            throw RegularException::forbidden('unmanaged coordinator task, can only managed owned task');
        }
    }

    //
    public function approveReport(): void
    {
        $this->task->approveReport();
    }

    public function askForReportRevision(): void
    {
        $this->task->askForReportRevision();
    }

}
