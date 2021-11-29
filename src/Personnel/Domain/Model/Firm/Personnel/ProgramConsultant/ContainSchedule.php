<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use SharedContext\Domain\ValueObject\Schedule;

interface ContainSchedule
{
    public function scheduleInConflictWith(Schedule $otherSchedule): bool;
}
