<?php

namespace Firm\Domain\Model\Firm;

interface IProgramTask
{
    public function execute(Program $program, $payload): void;
}
