<?php

namespace Query\Domain\Model\Firm\Program;

interface ITaskInProgramExecutableByParticipant
{

    public function executeTaskInProgram(string $programId): void;
}
