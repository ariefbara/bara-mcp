<?php

namespace Participant\Domain\Model\Participant;

use SharedContext\Domain\ValueObject\Schedule;

interface ContainSchedule
{
    public function aScheduledOrPotentialScheduleInConflictWith(ContainSchedule $other): bool;
    
    public function scheduleIntersectWith(Schedule $other): bool;
}
