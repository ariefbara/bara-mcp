<?php

namespace Firm\Domain\Model;

interface ResponsiveTask
{

    public function execute($payload): void;
}
