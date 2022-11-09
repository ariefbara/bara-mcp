<?php

namespace Personnel\Domain\Model\Firm\Personnel\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Note;

class CoordinatorNote
{

    /**
     * 
     * @var Coordinator
     */
    protected $coordinator;

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
     * @var Note
     */
    protected $note;

    /**
     * 
     * @var bool
     */
    protected $viewableByParticipant;

    public function __construct(
            Coordinator $coordinator, Participant $participant, string $id, string $content, bool $viewableByParticipant)
    {
        $this->coordinator = $coordinator;
        $this->participant = $participant;
        $this->id = $id;
        $this->note = new Note($id, $content);
        $this->viewableByParticipant = $viewableByParticipant;
    }
    
    public function update(string $content): void
    {
        $this->note->update($content);
    }
    
    public function showToParticipant(): void
    {
        $this->viewableByParticipant = true;
    }

    public function HideFromParticipant(): void
    {
        $this->viewableByParticipant = false;
    }
    
    public function remove(): void
    {
        $this->note->remove();
    }
    
    public function assertManageableByCoordinator(Coordinator $coordinator): void
    {
        if ($this->coordinator !== $coordinator) {
            throw RegularException::forbidden('unmanaged coordinator note, can only managed own note');
        }
    }
    
}
