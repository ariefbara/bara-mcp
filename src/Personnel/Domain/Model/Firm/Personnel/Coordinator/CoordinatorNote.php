<?php

namespace Personnel\Domain\Model\Firm\Personnel\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Model\Firm\Program\Participant;
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
//        $this->coordinator = $coordinator;
//        $this->participant = $participant;
//        $this->id = $id;
//        $this->note = $note;
//        $this->viewableByParticipant = $viewableByParticipant;
    }
    
    public function update(string $content): void
    {
        
    }
    
    public function showToParticipant(): void
    {
    }

    public function HideFromParticipant(): void
    {
    }
    
    public function remove(): void
    {
        
    }
    
}
