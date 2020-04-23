<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Mentor;

interface MentorRepository
{

    public function update(): void;

    public function ofId(ProgramCompositionId $programCompositionId, string $mentorId): Mentor;

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize);
}
