<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantNote;

interface ConsultantNoteRepository
{

    public function nextIdentity(): string;

    public function add(ConsultantNote $consultantNote): void;

    public function ofId(string $id): ConsultantNote;
}
