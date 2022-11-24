<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

interface MentorTask
{

    public function execute(ProgramConsultant $mentor, $payload): void;
}
