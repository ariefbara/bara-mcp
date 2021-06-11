<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;

interface MutationTaskExecutableByManager
{
    public function execute(Firm $firm): void;
}
