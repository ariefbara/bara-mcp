<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\Model\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\Note;

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

    public function __construct(Participant $participant, string $id, string $content)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->note = new Note($id, $content);
    }

    public function update(string $content): void
    {
        $this->note->update($content);
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
