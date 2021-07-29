<?php

namespace Firm\Domain\Model\Firm\Program;

interface ITaskExecutableByCoordinator
{
    public function executeByCoordinator(Coordinator $coordinator): void;
}
