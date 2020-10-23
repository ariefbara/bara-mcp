<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;

interface AssetInProgram
{
    public function belongsToProgram(Program $program): bool;
}
