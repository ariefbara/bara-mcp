<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant;

use Participant\Domain\Model\Participant\DeclaredMentoring;

interface DeclaredMentoringRepository
{

    public function nextIdentity(): string;

    public function add(DeclaredMentoring $declaredMentoring): void;

    public function ofId(string $id): DeclaredMentoring;
}
