<?php

namespace Firm\Application\Service\Manager\ManagerAttendee;

use Firm\Domain\Model\Firm\Manager\ManagerAttendee;

interface ManagerAttendeeRepository
{

    public function aManagerAttendeeBelongsToManager(string $firmId, string $managerId, string $attendeeId): ManagerAttendee;

    public function update(): void;
}
