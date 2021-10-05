<?php

namespace Query\Domain\Model\Firm;

interface ITaskInProgramExecutableByManager
{

    public function executeInProgram(Program $program): void;
}
