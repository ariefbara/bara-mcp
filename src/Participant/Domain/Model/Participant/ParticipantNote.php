<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\Model\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Note;
use SharedContext\Domain\ValueObject\LabelData;

class ParticipantNote
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
     * @var Note
     */
    protected $note;

    public function __construct(Participant $participant, string $id, LabelData $labelData)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->note = new Note($id, $labelData);
    }

    public function update(LabelData $labelData): void
    {
        $this->note->update($labelData);
    }

    public function remove(): void
    {
        $this->note->remove();
    }

    public function assertManageableByParticipant(Participant $participant): void
    {
        if ($this->participant !== $participant) {
            throw RegularException::forbidden('unmanaged participant note, can only manage owned note');
        }
    }

}
