<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function ofId(string $consultationRequestId): ConsultationRequest;

    public function update(): void;
}
