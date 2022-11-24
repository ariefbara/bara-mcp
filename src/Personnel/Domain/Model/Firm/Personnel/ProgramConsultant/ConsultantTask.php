<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\Task;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\LabelData;

class ConsultantTask
{

    /**
     * 
     * @var ProgramConsultant
     */
    protected $consultant;

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

    public function __construct(ProgramConsultant $consultant, Participant $participant, string $id, LabelData $data)
    {
        $this->consultant = $consultant;
        $this->id = $id;
        $this->task = new Task($participant, $id, $data);
    }

    public function update(LabelData $data): void
    {
        $this->task->update($data);
    }

    public function cancel(): void
    {
        $this->task->cancel();
    }

    public function assertManageableByConsultant(ProgramConsultant $consultant): void
    {
        if ($this->consultant !== $consultant) {
            throw RegularException::forbidden('unmanaged consultant task, can only managed owned task');
        }
    }

}
