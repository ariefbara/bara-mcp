<?php

namespace Firm\Application\Service\Personnel\ConsultantAttendee;

use Firm\Domain\Model\Firm\Program\Consultant\ConsultantAttendee;

interface ConsultantAttendeeRepository
{
    public function aConsultantAttendeeBelongsToPersonnel(string $firmId, string $personnelId, string $meetingId): ConsultantAttendee;
    
    public function update(): void;
}
