<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequest;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest\PersonnelNotificationOnConsultationRequest;

interface PersonnelNotificationOnConsultationRequestRepository
{

    public function add(PersonnelNotificationOnConsultationRequest $personnelNotificationOnConsultationRequest): void;

    public function nextIdentity(): string;
}
