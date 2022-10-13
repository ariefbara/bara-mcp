<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function nextIdentity(): string;

    public function add(ConsultationRequest $consultationRequest): void;
}
