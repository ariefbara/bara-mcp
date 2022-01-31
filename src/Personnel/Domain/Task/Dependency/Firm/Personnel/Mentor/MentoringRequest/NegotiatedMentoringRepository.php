<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequest;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest\NegotiatedMentoring;

interface NegotiatedMentoringRepository
{

    public function ofId(string $id): NegotiatedMentoring;
}
