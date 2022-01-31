<?php

namespace Query\Domain\Model\Firm;

interface TaskExecutableByPersonnel
{
    public function execute(string $personnelId): void;
}
