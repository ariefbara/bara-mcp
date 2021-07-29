<?php

namespace Firm\Application\Service\Personnel\CoordinatorAttendee;

use Firm\Domain\Model\Firm\Program\Coordinator\CoordinatorAttendee;

interface CoordinatorAttendeeRepository
{

    public function aCoordinatorAttendeeBelongsToPersonnel(string $firmId, string $personnelId, string $attendeeId): CoordinatorAttendee;
    
    public function update(): void;
}
