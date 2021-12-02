<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DeclaredMentoring;

interface DeclaredMentoringRepository
{

    public function nextIdentity(): string;

    public function add(DeclaredMentoring $declaredMentoring): void;

    public function ofId(string $id): DeclaredMentoring;
}
