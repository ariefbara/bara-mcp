<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Note;
use SharedContext\Domain\ValueObject\LabelData;

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
            ProgramConsultant $consultant, Participant $participant, string $id, LabelData $labelData,
            bool $viewableByParticipant)
    {
        $this->consultant = $consultant;
        $this->participant = $participant;
        $this->id = $id;
        $this->note = New Note($id, $labelData);
        $this->viewableByParticipant = $viewableByParticipant;
    }

    public function update(LabelData $labelData): void
    {
        $this->note->update($labelData);
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
