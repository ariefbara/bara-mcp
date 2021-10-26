<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

interface ConsultationSessionRepository
{

    public function nextIdentity(): string;

    public function add(ConsultationSession $consultationSession): void;

    public function aConsultationSessionOfId(string $id): ConsultationSession;
}
