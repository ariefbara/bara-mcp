<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;

interface MentoringRequestRepository
{

    public function ofId(string $id): MentoringRequest;
}
