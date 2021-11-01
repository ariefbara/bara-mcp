<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Program;

interface ITaskInProgramExecutableByConsultant
{

    public function executeTaskInProgram(Program $program): void;
}
