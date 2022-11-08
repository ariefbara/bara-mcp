<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Note;

class ConsultantNote
{

    /**
     * 
     * @var ProgramConsultant
     */
    protected $consultant;

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
            ProgramConsultant $consultant, Participant $participant, string $id, string $content,
            bool $viewableByParticipant)
    {
        $this->consultant = $consultant;
        $this->participant = $participant;
        $this->id = $id;
        $this->note = New Note($id, $content);
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
    
    public function assertManageableByConsultant(ProgramConsultant $consultant): void
    {
        if ($this->consultant !== $consultant) {
            throw RegularException::forbidden('can only manage own consultant note');
        }
    }

}
