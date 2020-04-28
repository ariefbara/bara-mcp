<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession\PersonnelNotificationOnConsultationSession;

interface PersonnelNotificationOnConsultationSessionRepository
{
    public function add(PersonnelNotificationOnConsultationSession $personnelNotificationOnConsultationSession): void;
    
    public function nextIdentity(): string;
}
