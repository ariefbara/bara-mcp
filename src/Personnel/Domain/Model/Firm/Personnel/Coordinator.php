<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel;
use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorNote;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Coordinator\CoordinatorTask;
use Resources\Exception\RegularException;

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
            string $coordinatorNoteId, Participant $participant, string $content, bool $viewableByParticipant): CoordinatorNote
    {
        $participant->assertUsableInProgram($this->programId);
        return new CoordinatorNote($this, $participant, $coordinatorNoteId, $content, $viewableByParticipant);
    }

}
