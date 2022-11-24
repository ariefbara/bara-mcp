<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel;
use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorNote;
use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorTask as CoordinatorTask2;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Coordinator\CoordinatorTask;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\LabelData;

class Coordinator
{

    /**
     * 
     * @var Personnel
     */
    protected $personnel;

    /**
     * 
     * @var string
     */
    protected $programId;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }

    public function executeTask(CoordinatorTask $task, $payload): void
    {
        if (!$this->active) {
            Throw RegularException::forbidden('only active coordinator can make this request');
        }
        $task->execute($this, $payload);
    }

    public function submitNote(
            string $coordinatorNoteId, Participant $participant, LabelData $labelData, bool $viewableByParticipant): CoordinatorNote
    {
        $participant->assertUsableInProgram($this->programId);
        return new CoordinatorNote($this, $participant, $coordinatorNoteId, $labelData, $viewableByParticipant);
    }
    
    public function submitTask(string $coordinatorTaskId, Participant $participant, LabelData $data): CoordinatorTask2
    {
        $participant->assertUsableInProgram($this->programId);
        return new CoordinatorTask2($this, $participant, $coordinatorTaskId, $data);
    }

}
