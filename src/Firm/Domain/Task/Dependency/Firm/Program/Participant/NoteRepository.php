<?php

namespace Firm\Domain\Task\Dependency\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\Participant\Note;

interface NoteRepository
{

    public function nextIdentity(): string;

    public function add(Note $note): void;

    public function ofId(string $id): Note;
}
